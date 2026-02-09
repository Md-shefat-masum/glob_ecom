<?php

namespace App\Http\Controllers;

use App\Models\MediaFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class MediaUploaderController extends Controller
{
    /**
     * Handle lazy file upload (FilePond process)
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
        ]);

        try {
            $file = $request->file('file');
            
            // Get width and height from request if provided
            $targetWidth = $request->input('width', null);
            $targetHeight = $request->input('height', null);
            
            // Get directory from request if provided
            $directory = $request->input('directory', '');
            
            // Generate unique filename
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            
            // Clean and limit filename to 20 chars + unique ID
            $cleanName = Str::slug(Str::limit($originalName, 20, ''));
            $uniqueId = Str::random(8);
            $fileName = $cleanName . '_' . $uniqueId . '.' . $extension;
            
            // Create folder structure: media/{directory}/{year}/{month} or media/{year}/{month}
            $year = now()->format('Y');
            $month = now()->format('m');
            if (!empty($directory)) {
                $folderPath = "uploads/media/{$directory}/{$year}/{$month}";
            } else {
                $folderPath = "uploads/media/{$year}/{$month}";
            }
            
            // Process image with Intervention Image
            $image = Image::make($file);
            
            // Get original dimensions
            $originalWidth = $image->width();
            $originalHeight = $image->height();
            
            // Resize if dimensions are provided
            if ($targetWidth && $targetHeight) {
                $image->fit($targetWidth, $targetHeight, function ($constraint) {
                    $constraint->upsize();
                });
            } elseif ($targetWidth || $targetHeight) {
                $image->resize($targetWidth, $targetHeight, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }
            
            // Get upload disk from env
            $disk = env('FILE_UPLOAD_DISK', 'public');
            
            // Encode image
            $encodedImage = $image->encode($extension, 85);
            
            // Store using Storage::put
            $filePath = $folderPath . '/' . $fileName;
            Storage::disk($disk)->put($filePath, $encodedImage);
            
            // Get file size
            $fileSize = Storage::disk($disk)->size($filePath);
            
            // Generate URL based on disk type
            $fileUrl = $this->getFileUrl($disk, $filePath);
            
            // Create media file record
            $mediaFile = MediaFile::create([
                'folder_path' => $folderPath,
                'file_path' => $filePath,
                'domain_url' => url('/'),
                'full_url' => $fileUrl,
                'file_name' => $fileName,
                'original_name' => $file->getClientOriginalName(),
                'size' => $fileSize,
                'mime_type' => $file->getMimeType(),
                'extension' => $extension,
                'width' => $image->width(),
                'height' => $image->height(),
                'disk' => $disk,
                'uploader_type' => auth()->check() ? get_class(auth()->user()) : null,
                'uploader_id' => auth()->id(),
                'file_type' => 'image',
                'is_temp' => true,
                'metadata' => [
                    'original_width' => $originalWidth,
                    'original_height' => $originalHeight,
                    'resized' => ($targetWidth || $targetHeight) ? true : false,
                ],
            ]);
            
            // Generate full URL using FILE_URL from env
            $fileUrl = $this->getFileUrl($disk, $filePath);
            
            // Return simple response
            return response()->json([
                'success' => true,
                'id' => $mediaFile->id,
                'path' => $filePath, // Include path for storage reference
                'url' => $fileUrl, // Full URL using FILE_URL from env
                'width' => $image->width(), // Image width
                'height' => $image->height(), // Image height
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Upload failed: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Handle file revert (FilePond revert - delete temporary file)
     */
    public function revert(Request $request)
    {
        try {
            $content = $request->getContent();
            $data = json_decode($content, true);
            
            $mediaId = $data['id'] ?? null;
            
            if (!$mediaId) {
                return response()->json(['error' => 'No file ID provided'], 400);
            }
            
            $mediaFile = MediaFile::find($mediaId);
            
            if ($mediaFile && $mediaFile->is_temp) {
                // Delete physical file
                $mediaFile->deleteFile();
                
                // Delete database record
                $mediaFile->forceDelete();
                
                return response()->json(['success' => true], 200);
            }
            
            return response()->json(['error' => 'File not found or not temporary'], 404);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Load existing file for FilePond
     */
    public function load($id)
    {
        try {
            $mediaFile = MediaFile::findOrFail($id);
            
            // Check if file exists - handle both storage disk and public directory
            $fileExists = false;
            $fileContent = null;
            
            // If file path starts with 'uploads/', it's in public directory
            if (strpos($mediaFile->file_path, 'uploads/') === 0) {
                $publicPath = public_path($mediaFile->file_path);
                if (File::exists($publicPath)) {
                    $fileExists = true;
                    $fileContent = File::get($publicPath);
                }
            } else {
                // Otherwise, check storage disk
                if ($mediaFile->exists()) {
                    $fileExists = true;
                    $fileContent = Storage::disk($mediaFile->disk)->get($mediaFile->file_path);
                }
            }
            
            if (!$fileExists || !$fileContent) {
                return response()->json(['error' => 'File not found'], 404);
            }
            
            return response($fileContent, 200)
                ->header('Content-Type', $mediaFile->mime_type)
                ->header('Content-Disposition', 'inline; filename="' . $mediaFile->file_name . '"');
                
        } catch (\Exception $e) {
            return response()->json(['error' => 'File not found: ' . $e->getMessage()], 404);
        }
    }

    /**
     * Fetch file metadata
     */
    public function fetch(Request $request)
    {
        try {
            $url = $request->input('url');
            
            if (!$url) {
                return response()->json(['error' => 'URL required'], 400);
            }
            
            // Extract file ID from URL if it's a stored media file
            // This is for fetching already uploaded files
            $mediaFile = MediaFile::where('full_url', $url)->first();
            
            if (!$mediaFile) {
                return response()->json(['error' => 'File not found'], 404);
            }
            
            // Check if file exists - handle both storage disk and public directory
            $fileExists = false;
            $fileContent = null;
            
            // If file path starts with 'uploads/', it's in public directory
            if (strpos($mediaFile->file_path, 'uploads/') === 0) {
                $publicPath = public_path($mediaFile->file_path);
                if (File::exists($publicPath)) {
                    $fileExists = true;
                    $fileContent = File::get($publicPath);
                }
            } else {
                // Otherwise, check storage disk
                if ($mediaFile->exists()) {
                    $fileExists = true;
                    $fileContent = Storage::disk($mediaFile->disk)->get($mediaFile->file_path);
                }
            }
            
            if (!$fileExists || !$fileContent) {
                return response()->json(['error' => 'File not found'], 404);
            }
            
            return response($fileContent, 200)
                ->header('Content-Type', $mediaFile->mime_type)
                ->header('Content-Disposition', 'inline; filename="' . $mediaFile->file_name . '"');
                
        } catch (\Exception $e) {
            return response()->json(['error' => 'Fetch failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mark uploaded files as permanent (called when form is saved)
     */
    public function markAsPermanent(Request $request)
    {
        $request->validate([
            'file_ids' => 'required|array',
            'file_ids.*' => 'exists:media_files,id',
        ]);
        
        try {
            $fileIds = $request->input('file_ids');
            
            MediaFile::whereIn('id', $fileIds)
                ->update([
                    'is_temp' => false,
                    'temp_token' => null,
                ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Files marked as permanent',
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete a media file permanently
     */
    public function destroy($id)
    {
        try {
            $mediaFile = MediaFile::findOrFail($id);
            
            // Delete physical file
            $mediaFile->deleteFile();
            
            // Delete database record
            $mediaFile->forceDelete();
            
            return response()->json([
                'success' => true,
                'message' => 'File deleted successfully',
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete media file by path or ID
     */
    public function deleteByPath(Request $request)
    {
        try {
            $mediaId = $request->input('media_id');
            $filePath = $request->input('file_path');
            
            $mediaFile = null;
            
            // Try to find by media ID first
            if ($mediaId) {
                $mediaFile = MediaFile::find($mediaId);
            }
            
            // If not found by ID, try to find by file path
            if (!$mediaFile && $filePath) {
                $mediaFile = MediaFile::where('file_path', $filePath)->first();
            }
            
            if ($mediaFile) {
                // Delete physical file
                $mediaFile->deleteFile();
                
                // Delete database record
                $mediaFile->forceDelete();
                
                return response()->json([
                    'success' => true,
                    'message' => 'File deleted successfully',
                ]);
            }
            
            // If media file not found in database, try to delete physical file directly
            if ($filePath) {
                $disk = env('FILE_UPLOAD_DISK', 'public');
                $deleted = false;
                
                // Check if file path starts with 'uploads/' (public directory)
                if (strpos($filePath, 'uploads/') === 0) {
                    $publicPath = public_path($filePath);
                    if (File::exists($publicPath)) {
                        File::delete($publicPath);
                        $deleted = true;
                    }
                } else {
                    // Try storage disk
                    if (Storage::disk($disk)->exists($filePath)) {
                        Storage::disk($disk)->delete($filePath);
                        $deleted = true;
                    }
                    
                    // Also try old format paths (like "banner/image.jpg" in public directory)
                    $oldFormatPath = public_path($filePath);
                    if (File::exists($oldFormatPath)) {
                        File::delete($oldFormatPath);
                        $deleted = true;
                    }
                }
                
                return response()->json([
                    'success' => true,
                    'message' => $deleted ? 'File deleted successfully' : 'File not found or already deleted',
                ]);
            }
            
            return response()->json([
                'error' => 'No file path or media ID provided'
            ], 400);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Delete failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Find media file by path
     */
    public function findByPath(Request $request)
    {
        try {
            $filePath = $request->input('path');
            
            if (!$filePath) {
                return response()->json([
                    'error' => 'File path required'
                ], 400);
            }
            
            $mediaFile = MediaFile::where('file_path', $filePath)->first();
            
            if ($mediaFile) {
                return response()->json([
                    'success' => true,
                    'id' => $mediaFile->id,
                    'data' => $mediaFile,
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Media file not found'
            ], 404);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Search failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get media file info
     */
    public function show($id)
    {
        try {
            $mediaFile = MediaFile::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $mediaFile,
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'File not found'], 404);
        }
    }

    /**
     * Replace existing file (delete old, upload new)
     */
    public function replace(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);
        
        try {
            // Find and delete old file
            $oldMediaFile = MediaFile::find($id);
            if ($oldMediaFile) {
                $oldMediaFile->deleteFile();
                $oldMediaFile->forceDelete();
            }
            
            // Upload new file using the upload method
            return $this->upload($request);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get file URL based on disk type
     */
    private function getFileUrl(string $disk, string $filePath): string
    {
        // Get base URL from FILE_URL env, fallback to APP_URL
        $baseUrl = env('FILE_URL', env('APP_URL', url('/')));
        $baseUrl = rtrim($baseUrl, '/');
        
        // For public disk, construct URL
        if ($disk === 'public') {
            // Public disk is configured to use /uploads in filesystems.php
            return $baseUrl . '/uploads/' . ltrim($filePath, '/');
        }
        
        // For other disks (s3, ftp, etc.), try to get URL from storage
        try {
            /** @var \Illuminate\Filesystem\FilesystemAdapter $storage */
            $storage = Storage::disk($disk);
            
            // Check if disk config has URL
            $diskConfig = config("filesystems.disks.{$disk}");
            if (isset($diskConfig['url'])) {
                $diskUrl = rtrim($diskConfig['url'], '/');
                // If disk URL is relative, prepend base URL
                if (!filter_var($diskUrl, FILTER_VALIDATE_URL)) {
                    return $baseUrl . '/' . ltrim($diskUrl, '/') . '/' . ltrim($filePath, '/');
                }
                return $diskUrl . '/' . ltrim($filePath, '/');
            }
            
            // Try url() method if available (for S3, etc.)
            if (method_exists($storage, 'url')) {
                /** @phpstan-ignore-next-line */
                $storageUrl = $storage->url($filePath);
                // If storage returns relative URL, prepend base URL
                if (!filter_var($storageUrl, FILTER_VALIDATE_URL)) {
                    return $baseUrl . '/' . ltrim($storageUrl, '/');
                }
                return $storageUrl;
            }
        } catch (\Exception $e) {
            // Fall through to default
        }
        
        // Default fallback
        return $baseUrl . '/uploads/' . ltrim($filePath, '/');
    }
}
