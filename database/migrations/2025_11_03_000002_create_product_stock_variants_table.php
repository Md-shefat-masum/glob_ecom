<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductStockVariantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_stock_variants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('combination_key')->nullable(); // e.g., "Color:Red|Size:M|Material:Cotton"
            $table->json('variant_values')->nullable(); // Store variant values as JSON
            
            // Stock management
            $table->double('stock')->default(0);
            $table->integer('low_stock_alert')->nullable();
            
            // Pricing
            $table->double('additional_price')->default(0)->comment('Additional price on top of base price');
            $table->double('price')->nullable()->comment('Override base price if set');
            $table->double('discount_price')->nullable();
            
            // Identifiers
            $table->string('sku', 100)->nullable();
            $table->string('barcode', 100)->nullable();
            $table->string('image')->nullable();
            
            $table->tinyInteger('status')->default(1)->comment('1=>Active; 0=>Inactive');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->index('combination_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_stock_variants');
    }
}

