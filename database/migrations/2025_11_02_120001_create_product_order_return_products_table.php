<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductOrderReturnProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_order_return_products', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('product_order_return_id')->nullable();
            $table->unsignedBigInteger('product_order_product_id')->nullable()->comment('Reference to original order product');
            $table->unsignedBigInteger('product_warehouse_id')->nullable();
            $table->unsignedBigInteger('product_warehouse_room_id')->nullable();
            $table->unsignedBigInteger('product_warehouse_room_cartoon_id')->nullable();
            
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('product_name')->nullable();
            
            $table->unsignedInteger('qty')->nullable();
            $table->decimal('sale_price', 10, 2)->nullable();
            
            $table->string('discount_type', 50)->nullable();
            $table->decimal('discount_amount', 10, 2)->nullable();
            $table->decimal('tax', 10, 2)->nullable();
            
            $table->decimal('total_price', 10, 2)->nullable();
            $table->decimal('product_price', 10, 2)->nullable()->comment('Original product cost price');
            
            $table->string('slug')->nullable();
            $table->unsignedBigInteger('creator')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            // Add indexes
            $table->index('product_order_return_id');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_order_return_products');
    }
}

