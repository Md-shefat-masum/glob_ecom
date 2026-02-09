<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\MediaFolder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class MediaUpController extends Controller
{
    public function upload($source = [], $inner_call = false)
    {
        if (count($source) == 0) {
            $source = request()->all();
        }

        $rules = [
            'file' => 'required|mimes:jpeg,png,jpg,webp,pdf|max:15000',
            'folder' => 'required|max:50|min:1|string',
            'disk' => ['required', 'in:public,s3,ftp'],
            'media_folder_id' => ['required', 'exists:media_folders,id'],
        ];

        $mime = request()->file('file')->getMimeType();
        if (str_starts_with($mime, 'image/')) {
            $rules['height'] = ['required', 'numeric', 'min:40', 'max:1080'];
            $rules['width'] = ['required', 'numeric', 'min:40', 'max:1920'];
        }
        
        $validator = Validator::make($source, $rules, [
            'file.required' => 'There is no file to upload',
            'folder.required' => 'Folder name is required',
        ]);

        if ($validator->fails()) {
            return entityResponse($validator->errors(), 422, 'error', 'Validation Error');
        }

        $path = null;
        $file = $source['file'];
        $folder = $source['folder'];
        $maxHeight = $source['height'];
        $maxWidth = $source['width'];
        $disk = $source['disk'];
        $media_folder_id = $source['media_folder_id'] ?? 0;

        try {
            $path = $this->resizeAndSaveImage($file, $folder, $maxHeight, $maxWidth, $disk);
        } catch (\Throwable $th) {
            // dd($th->getMessage());
        }

        if (!$path) {
            $file_name  = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $file_name  = preg_replace('/[^A-Za-z0-9_\-]/', '_', $file_name);
            // $file_name .= '-';
            // $file_name .= uniqid();
            $file_name .= '.';
            $file_name .= $file->getClientOriginalExtension();

            $path =  Storage::disk($disk)->putFileAs($folder, $file, $file_name);
        }

        $fileUrl = Storage::disk($disk)->url($path);

        $dirPath = trim(dirname($path), '/');
        $parts = explode('/', $dirPath);
        $folders = [];
        $current = '';

        foreach ($parts as $part) {
            $current .= ($current ? '/' : '') . $part;
            $folders[] = $current;
        }

        $path = trim($path, '/');
        $media = \App\Models\Media::create([
            'disk'      => $disk,
            'path'      => $path,

            'filename'  => basename($path),
            'extension' => pathinfo($path, PATHINFO_EXTENSION),
            'mime_type' => $file->getMimeType(),
            'size'      => $file->getSize(),

            // 'url'       => $fileUrl,
            'folders'   => json_encode($folders),
            'media_folder_id'   => $media_folder_id ?? 1,
        ]);

        if ($inner_call) {
            return $media;
        }

        return entityResponse([
            'path' => $path,
            'media' => $media,
            'url' => $fileUrl,
        ]);
    }

    public function delete($source = [])
    {
        if (count($source) == 0) {
            $source = request()->all();
        }

        $validator = Validator::make($source, [
            'path' => 'required',
            'disk' => ['required', 'in:public,s3'],
        ]);

        if ($validator->fails()) {
            return entityResponse($validator->errors(), 422, 'error', 'Validation Error');
        }

        try {
            Storage::disk($source['disk'])->delete($source['path']);
        } catch (\Throwable $th) {
            return entityResponse("failed to delete " . $source['path'] . ". " . $th->getMessage());
        }

        try {
            Media::where('path', $source['path'])->delete();
        } catch (\Throwable $th) {
            //throw $th;
        }

        return entityResponse("deleted " . $source['path']);
    }

    public function resizeAndSaveImage($file, $folder = 'images', $maxHeight = 200, $maxWidth = 200, $disk = 'public')
    {
        $imagePath = $file->getPathname();
        $mime = $file->getMimeType();
        $folder = $folder;

        if (!in_array($mime, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
            return false;
        }

        $img = Image::make($file);
        $canvas = Image::canvas($maxWidth, $maxHeight);
        $img->resize($maxWidth, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $canvas->insert($img, 'center');
        $imgStream = (string) $canvas->encode();

        $file_name  = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $file_name  = preg_replace('/[^A-Za-z0-9_\-]/', '_', $file_name);
        // $file_name .= '-';
        // $file_name .= uniqid();
        $file_name = substr($file_name, 0, 20);
        $file_name .= '.';
        $file_name .= $file->getClientOriginalExtension();

        $file_source_path = $folder . '/' . $file_name;

        $storagePath = Storage::disk($disk)->put($file_source_path, $imgStream);
        // dd($storagePath, $file_source_path);

        return $storagePath ? $file_source_path : false;
    }

    public function uploadImage(Request $request)
    {
        // $path = $this->resizeAndSaveImage(request()->file('file'), '/storage/post_image', 400, 600, 's3');
        // $url = Storage::disk('s3')->url($path);

        $media = $this->upload(
            source: [
                'file' => request()->file('file'),
                'folder' => '/storage/post_image',
                'height' => 400,
                'width' => 600,
                'disk' => 's3'
            ],
            inner_call: true
        );

        return response()->json(["location" => $media->url]);
    }

    public function folders()
    {
        $select = ['id', 'name', 'parent_id'];

        $data = MediaFolder::select($select)
            ->where('parent_id', 0)
            ->with([
                'children' => function ($query) use ($select) {
                    $query->select($select);
                    // $query->with([
                    //     'parent' => function ($query) {
                    //         $query->select(['id', 'name']);
                    //     }
                    // ]);
                },
                // 'parent' => function ($query) {
                //     $query->select(['id', 'name']);
                // }
            ])
            ->get();

        return entityResponse($data);
    }

    public function new_folder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'parent_id' => 'required',
        ]);

        if ($validator->fails()) {
            return entityResponse($validator->errors(), 422, 'error', 'Validation Error');
        }

        $data = MediaFolder::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
        ]);

        return entityResponse([
            ...$data->toArray(),
            'children' => [],
            'parent' => [],
        ]);
    }

    public function files_by_folder_id($media_directory_id)
    {
        $data = Media::where('media_directory_id', $media_directory_id)
            ->orderBy('id', 'DESC')
            ->where('status', 1)
            ->paginate(20);
        return entityResponse($data);
    }
}
