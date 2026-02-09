<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('media_files', function (Blueprint $table) {
            $table->id();
            $table->string('folder_path'); // uploads/media/2025/11
            $table->string('file_path'); // Full relative path from storage
            $table->string('domain_url')->nullable(); // Base domain URL
            $table->text('full_url')->nullable(); // Complete accessible URL
            $table->string('file_name'); // Original renamed file name
            $table->string('original_name')->nullable(); // Original uploaded name
            $table->unsignedBigInteger('size')->default(0); // File size in bytes
            $table->string('mime_type')->nullable(); // image/jpeg, image/png, etc
            $table->string('extension', 10)->nullable(); // jpg, png, etc
            $table->unsignedInteger('width')->nullable(); // Image width
            $table->unsignedInteger('height')->nullable(); // Image height
            $table->string('disk', 50)->default('public'); // Storage disk name
            $table->string('uploader_type')->nullable(); // User, Admin, etc
            $table->unsignedBigInteger('uploader_id')->nullable(); // ID of uploader
            $table->string('file_type', 50)->default('image'); // image, document, video, etc
            $table->boolean('is_temp')->default(true); // Track if file is temporary
            $table->string('temp_token', 100)->nullable(); // Token for lazy upload tracking
            $table->text('metadata')->nullable(); // JSON field for additional data
            $table->timestamps();
            $table->softDeletes(); // Soft delete for file recovery
            
            // Indexes
            $table->index('temp_token');
            $table->index('is_temp');
            $table->index('file_type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_files');
    }
};
