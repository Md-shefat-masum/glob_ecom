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
        // Add variant support to product_stocks table
        Schema::table('product_stocks', function (Blueprint $table) {
            // Variant identification
            $table->boolean('has_variant')->default(false)->after('product_id')
                ->comment('Flag to indicate if this stock is for a product variant');
            
            $table->string('variant_combination_key', 255)->nullable()->after('has_variant')
                ->comment('Variant combination key (e.g., Red-SM-Cotton)');
            
            $table->string('variant_sku', 100)->nullable()->after('variant_combination_key')
                ->comment('Variant SKU for identification');
            
            $table->string('variant_barcode', 50)->nullable()->after('variant_sku')
                ->comment('Variant barcode');
            
            // Variant details stored as JSON
            $table->json('variant_data')->nullable()->after('variant_barcode')
                ->comment('JSON data containing variant attributes: {color: "Red", size: "SM", material: "Cotton"}');
            
            // Variant pricing (can override product price)
            $table->decimal('variant_price', 10, 2)->nullable()->after('variant_data')
                ->comment('Variant specific price (overrides product price if set)');
            
            $table->decimal('variant_discount_price', 10, 2)->nullable()->after('variant_price')
                ->comment('Variant discount price');
            
            // Add indexes for better query performance
            $table->index('has_variant');
            $table->index('variant_combination_key');
            $table->index('variant_sku');
            $table->index('variant_barcode');
        });

        // Add variant support to product_stock_logs table
        Schema::table('product_stock_logs', function (Blueprint $table) {
            // Variant identification for logs
            $table->boolean('has_variant')->default(false)->after('product_id')
                ->comment('Flag to indicate if this log is for a product variant');
            
            $table->string('variant_combination_key', 255)->nullable()->after('has_variant')
                ->comment('Variant combination key');
            
            $table->string('variant_sku', 100)->nullable()->after('variant_combination_key')
                ->comment('Variant SKU');
            
            $table->json('variant_data')->nullable()->after('variant_sku')
                ->comment('JSON data containing variant attributes');
            
            // Add indexes
            $table->index('has_variant');
            $table->index('variant_combination_key');
            $table->index('variant_sku');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove variant support from product_stocks table
        Schema::table('product_stocks', function (Blueprint $table) {
            $table->dropIndex(['has_variant']);
            $table->dropIndex(['variant_combination_key']);
            $table->dropIndex(['variant_sku']);
            $table->dropIndex(['variant_barcode']);
            
            $table->dropColumn([
                'has_variant',
                'variant_combination_key',
                'variant_sku',
                'variant_barcode',
                'variant_data',
                'variant_price',
                'variant_discount_price'
            ]);
        });

        // Remove variant support from product_stock_logs table
        Schema::table('product_stock_logs', function (Blueprint $table) {
            $table->dropIndex(['has_variant']);
            $table->dropIndex(['variant_combination_key']);
            $table->dropIndex(['variant_sku']);
            
            $table->dropColumn([
                'has_variant',
                'variant_combination_key',
                'variant_sku',
                'variant_data'
            ]);
        });
    }
};
