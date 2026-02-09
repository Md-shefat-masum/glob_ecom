<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class MediaFile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'folder_path',
        'file_path',
        'domain_url',
        'full_url',
        'file_name',
        'original_name',
        'size',
        'mime_type',
        'extension',
        'width',
        'height',
        'disk',
        'uploader_type',
        'uploader_id',
        'file_type',
        'is_temp',
        'temp_token',
        'metadata',
    ];

    protected $casts = [
        'is_temp' => 'boolean',
        'size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'metadata' => 'array',
    ];

    /**
     * Get the full URL of the file
     */
    public function getUrlAttribute()
    {
        if ($this->full_url) {
            return $this->full_url;
        }
        
        return Storage::disk($this->disk)->url($this->file_path);
    }

    /**
     * Get the full storage path
     */
    public function getStoragePathAttribute()
    {
        return Storage::disk($this->disk)->path($this->file_path);
    }

    /**
     * Check if file exists in storage or public directory
     */
    public function exists(): bool
    {
        // If file path starts with 'uploads/', it's in public directory
        if (strpos($this->file_path, 'uploads/') === 0) {
            return File::exists(public_path($this->file_path));
        }
        
        // Otherwise, check storage disk
        return Storage::disk($this->disk)->exists($this->file_path);
    }

    /**
     * Delete file from storage
     */
    public function deleteFile(): bool
    {
        try {
            // If file path starts with 'uploads/', it's in public directory
            if (strpos($this->file_path, 'uploads/') === 0) {
                $publicPath = public_path($this->file_path);
                if (File::exists($publicPath)) {
                    return File::delete($publicPath);
                }
                return true;
            }
            
            // Otherwise, use storage disk
            if ($this->exists()) {
                return Storage::disk($this->disk)->delete($this->file_path);
            }
            
            return true;
        } catch (\Exception $e) {
            // Log error but don't throw
            \Log::error('Error deleting media file: ' . $e->getMessage(), [
                'file_path' => $this->file_path,
                'disk' => $this->disk
            ]);
            return false;
        }
    }

    /**
     * Mark file as permanent (not temp)
     */
    public function markAsPermanent(): bool
    {
        return $this->update([
            'is_temp' => false,
            'temp_token' => null,
        ]);
    }

    /**
     * Scope to get temporary files
     */
    public function scopeTemporary($query)
    {
        return $query->where('is_temp', true);
    }

    /**
     * Scope to get permanent files
     */
    public function scopePermanent($query)
    {
        return $query->where('is_temp', false);
    }

    /**
     * Scope to get files by type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('file_type', $type);
    }

    /**
     * Clean up old temporary files (older than 24 hours)
     */
    public static function cleanupOldTempFiles()
    {
        $oldFiles = static::temporary()
            ->where('created_at', '<', now()->subHours(24))
            ->get();

        foreach ($oldFiles as $file) {
            $file->deleteFile();
            $file->forceDelete();
        }

        return $oldFiles->count();
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        // When deleting, also delete the physical file
        static::deleting(function ($media) {
            if ($media->isForceDeleting()) {
                $media->deleteFile();
            }
        });
    }
}
