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
        Schema::create('package_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->nullable()->unique();
            $table->string('package_code')->unique();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('tagline')->nullable();
            $table->enum('status', ['draft', 'active', 'inactive', 'archived'])->default('draft');
            $table->enum('visibility', ['private', 'public', 'scheduled'])->default('private');
            $table->timestamp('publish_at')->nullable();
            $table->decimal('package_price', 15, 2)->default(0);
            $table->decimal('compare_at_price', 15, 2)->default(0);
            $table->decimal('calculated_savings_amount', 15, 2)->default(0);
            $table->decimal('calculated_savings_percent', 8, 2)->default(0);
            $table->json('pricing_breakdown')->nullable();
            $table->json('hero_section')->nullable();
            $table->json('content_blocks')->nullable();
            $table->foreignId('primary_media_id')->nullable()->constrained('media_files')->nullOnDelete();
            $table->json('gallery_media_ids')->nullable();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->foreignId('meta_image_id')->nullable()->constrained('media_files')->nullOnDelete();
            $table->json('landing_settings')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_products');
    }
};

