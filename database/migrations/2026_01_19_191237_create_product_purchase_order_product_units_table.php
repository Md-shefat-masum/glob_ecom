<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductPurchaseOrderProductUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_purchase_order_product_units', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('product_purchase_order_id')->nullable();
            $table->unsignedBigInteger('product_purchase_order_product_id')->nullable();
            $table->unsignedBigInteger('variant_combination_id')->nullable();
            $table->string('code', 10)->nullable();
            $table->unsignedFloat('price')->nullable();

            $table->enum('unit_status', ['instock', 'sold', 'returned', 'lost', 'damaged'])
                ->default('instock')
                ->nullable()
                ->comment('instock=>In stock; sold=>Sold; returned=>Returned; lost=>Lost; damaged=>Damaged');

            $table->unsignedBigInteger('creator')->nullable();
            $table->string('slug',)->nullable();
            $table->tinyInteger('status')->default(1)->comment('1=>Active; 0=>Inactive');

            $table->timestamps();

            $table->foreign('product_purchase_order_id')->references('id')->on('product_purchase_orders')->onDelete('cascade');
            $table->foreign('product_purchase_order_product_id')->references('id')->on('product_purchase_order_products')->onDelete('cascade');
            $table->foreign('variant_combination_id')->references('id')->on('product_variant_combinations')->onDelete('cascade');

            $table->index('product_purchase_order_id');
            $table->index('product_purchase_order_product_id');
            $table->index('variant_combination_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_purchase_order_product_units');
    }
}
