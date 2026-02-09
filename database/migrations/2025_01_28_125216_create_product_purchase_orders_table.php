<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductPurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_purchase_orders', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('product_warehouse_id')->nullable();
            $table->unsignedBigInteger('product_warehouse_room_id')->nullable();
            $table->unsignedBigInteger('product_warehouse_room_cartoon_id')->nullable();
            $table->unsignedBigInteger('product_supplier_id')->nullable();
            $table->unsignedBigInteger('product_purchase_quotation_id')->nullable();             
            $table->date('date')->nullable();                 
            $table->string('discount_type')->nullable();
            $table->integer('discount_amount')->nullable();
            $table->decimal('calculated_discount_amount', 6, 2)->nullable();
            $table->enum('order_status', ['pending', 'received'])->default('pending');
            $table->string('other_charge_type', 100)->nullable();
            $table->string('other_charge_type', 100)->nullable();
            $table->decimal('other_charge_percentage', 6, 2)->nullable();
            $table->decimal('other_charge_amount', 10, 2)->nullable();
            $table->decimal('round_off', 10, 2)->nullable();
            $table->decimal('total', 10, 2)->nullable();
            $table->text('note')->nullable();
            $table->string('code')->nullable();
            $table->string('reference')->nullable();

            $table->unsignedBigInteger('creator')->nullable();
            $table->string('slug',)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_purchase_orders');
    }
}
