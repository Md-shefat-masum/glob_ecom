<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductVariantCombinationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_variant_combinations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            
            // Combination key: Red-SM-50kg-Cotton
            $table->string('combination_key')->index();
            
            // Variant values as JSON: {"color": "Red", "size": "SM", "weight": "50kg", "material": "Cotton"}
            $table->json('variant_values');
            
            // Pricing
            $table->double('price')->nullable()->comment('Override product base price');
            $table->double('discount_price')->nullable();
            $table->double('additional_price')->nullable()->default(0)->comment('Added to base price if price is null');
            
            // Stock management
            $table->double('stock')->default(0);
            $table->integer('low_stock_alert')->nullable();
            
            // Identifiers
            $table->string('sku', 100)->nullable();
            $table->string('barcode', 100)->nullable();
            
            // Media
            $table->string('image')->nullable();
            
            $table->tinyInteger('status')->default(1)->comment('1=>Active; 0=>Inactive');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->unique(['product_id', 'combination_key'], 'unique_product_combination');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_variant_combinations');
    }
}

