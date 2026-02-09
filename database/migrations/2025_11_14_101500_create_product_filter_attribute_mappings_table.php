<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductFilterAttributeMappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_filter_attribute_mappings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('variant_group_id');
            $table->unsignedBigInteger('variant_key_id');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('subcategory_id')->nullable();
            $table->unsignedBigInteger('childcategory_id')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->timestamps();

            $table->foreign('variant_group_id')
                ->references('id')
                ->on('product_stock_variant_groups')
                ->onDelete('cascade');

            $table->foreign('variant_key_id')
                ->references('id')
                ->on('product_stock_variants_group_keys')
                ->onDelete('cascade');

            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');

            $table->foreign('subcategory_id')
                ->references('id')
                ->on('subcategories')
                ->onDelete('cascade');

            $table->foreign('childcategory_id')
                ->references('id')
                ->on('child_categories')
                ->onDelete('cascade');

            $table->foreign('brand_id')
                ->references('id')
                ->on('brands')
                ->onDelete('cascade');

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');

            $table->index(['variant_group_id', 'variant_key_id'], 'pfa_mapping_group_key_idx');
            $table->index(['category_id', 'variant_group_id'], 'pfa_mapping_category_idx');
            $table->index(['subcategory_id', 'variant_group_id'], 'pfa_mapping_subcategory_idx');
            $table->index(['childcategory_id', 'variant_group_id'], 'pfa_mapping_childcategory_idx');
            $table->index(['brand_id', 'variant_group_id'], 'pfa_mapping_brand_idx');
            $table->index(['product_id', 'variant_group_id'], 'pfa_mapping_product_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_filter_attribute_mappings');
    }
}

