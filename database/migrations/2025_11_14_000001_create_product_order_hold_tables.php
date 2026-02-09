<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductOrderHoldTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('product_order_hold')) {
            Schema::create('product_order_hold', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->unsignedBigInteger('customer_id')->nullable()->index();
                $table->unsignedBigInteger('product_warehouse_id')->nullable()->index();
                $table->decimal('subtotal', 15, 2)->default(0);
                $table->decimal('discount_amount', 15, 2)->default(0);
                $table->decimal('coupon_amount', 15, 2)->default(0);
                $table->decimal('extra_charge', 15, 2)->default(0);
                $table->decimal('delivery_charge', 15, 2)->default(0);
                $table->decimal('round_off', 15, 2)->default(0);
                $table->decimal('grand_total', 15, 2)->default(0);
                $table->json('meta')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('product_order_hold_items')) {
            Schema::create('product_order_hold_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('hold_id')->index();
                $table->unsignedBigInteger('product_id')->index();
                $table->unsignedBigInteger('variant_id')->nullable()->index();
                $table->string('title')->nullable();
                $table->integer('qty')->default(0);
                $table->decimal('unit_price', 15, 2)->default(0);
                $table->decimal('discount_amount', 15, 2)->default(0);
                $table->decimal('final_price', 15, 2)->default(0);
                $table->json('meta')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_order_hold_items');
        Schema::dropIfExists('product_order_hold');
    }
}


