<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('package_product_items', function (Blueprint $table) {
            if (!Schema::hasColumn('package_product_items', 'package_id')) {
                $table->unsignedBigInteger('package_id')->nullable()->after('id');
            }

            if (!Schema::hasColumn('package_product_items', 'product_variant_id')) {
                $table->unsignedBigInteger('product_variant_id')->nullable()->after('product_id');
            }

            if (!Schema::hasColumn('package_product_items', 'variant_combination_id')) {
                $table->unsignedBigInteger('variant_combination_id')->nullable()->after('product_variant_id');
            }

            if (!Schema::hasColumn('package_product_items', 'variant_snapshot')) {
                $table->json('variant_snapshot')->nullable()->after('size_id');
            }

            if (!Schema::hasColumn('package_product_items', 'unit_price')) {
                $table->decimal('unit_price', 15, 2)->nullable()->after('quantity');
            }

            if (!Schema::hasColumn('package_product_items', 'compare_at_price')) {
                $table->decimal('compare_at_price', 15, 2)->nullable()->after('unit_price');
            }

            if (!Schema::hasColumn('package_product_items', 'position')) {
                $table->unsignedInteger('position')->default(0)->after('compare_at_price');
            }
        });

        Schema::table('package_product_items', function (Blueprint $table) {
            $table->foreign('package_id')->references('id')->on('package_products')->cascadeOnDelete();
            $table->foreign('product_variant_id')->references('id')->on('product_variants')->nullOnDelete();
            $table->foreign('variant_combination_id')->references('id')->on('product_variant_combinations')->nullOnDelete();
        });

        // Seed package_products table with existing package entries if needed
        $existingPackages = DB::table('products')
            ->where('is_package', 1)
            ->get();

        foreach ($existingPackages as $package) {
            $exists = DB::table('package_products')->where('product_id', $package->id)->first();

            if ($exists) {
                continue;
            }

            $title = $package->name ?? 'Package #' . $package->id;
            $slug = Str::slug($title);
            $baseSlug = $slug;
            $suffix = 1;

            while (DB::table('package_products')->where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $suffix++;
            }

            $packageId = DB::table('package_products')->insertGetId([
                'product_id' => $package->id,
                'package_code' => 'PKG-' . str_pad($package->id, 6, '0', STR_PAD_LEFT),
                'title' => $title,
                'slug' => $slug,
                'tagline' => $package->short_description,
                'status' => $package->status == 1 ? 'active' : 'inactive',
                'visibility' => 'public',
                'publish_at' => $package->created_at,
                'package_price' => $package->discount_price ?? $package->price ?? 0,
                'compare_at_price' => $package->price ?? 0,
                'calculated_savings_amount' => max(0, ($package->price ?? 0) - ($package->discount_price ?? $package->price ?? 0)),
                'calculated_savings_percent' => 0,
                'pricing_breakdown' => null,
                'hero_section' => null,
                'content_blocks' => null,
                'primary_media_id' => null,
                'gallery_media_ids' => null,
                'short_description' => $package->short_description,
                'description' => $package->description,
                'meta_title' => $package->meta_title,
                'meta_description' => $package->meta_description,
                'meta_keywords' => $package->meta_keywords,
                'meta_image_id' => null,
                'landing_settings' => null,
                'created_by' => $package->created_by ?? null,
                'updated_by' => $package->updated_by ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('package_product_items')
                ->where('package_product_id', $package->id)
                ->update(['package_id' => $packageId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('package_product_items', function (Blueprint $table) {
            $table->dropForeign(['package_id']);
            $table->dropForeign(['product_variant_id']);
            $table->dropForeign(['variant_combination_id']);

            if (Schema::hasColumn('package_product_items', 'package_id')) {
                $table->dropColumn('package_id');
            }

            if (Schema::hasColumn('package_product_items', 'product_variant_id')) {
                $table->dropColumn('product_variant_id');
            }

            if (Schema::hasColumn('package_product_items', 'variant_combination_id')) {
                $table->dropColumn('variant_combination_id');
            }

            if (Schema::hasColumn('package_product_items', 'variant_snapshot')) {
                $table->dropColumn('variant_snapshot');
            }

            if (Schema::hasColumn('package_product_items', 'unit_price')) {
                $table->dropColumn('unit_price');
            }

            if (Schema::hasColumn('package_product_items', 'compare_at_price')) {
                $table->dropColumn('compare_at_price');
            }

            if (Schema::hasColumn('package_product_items', 'position')) {
                $table->dropColumn('position');
            }
        });
    }
};

