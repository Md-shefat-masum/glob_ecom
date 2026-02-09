<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id')->nullable();
            $table->string('order_code', 192)->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('product_warehouse_id')->nullable();
            $table->unsignedBigInteger('product_warehouse_room_id')->nullable();
            $table->unsignedBigInteger('product_warehouse_room_cartoon_id')->nullable();
            $table->unsignedBigInteger('product_supplier_id')->nullable();
            $table->unsignedBigInteger('product_order_quotation_id')->nullable();
            $table->date('sale_date')->nullable();
            $table->date('due_date')->nullable();
            $table->decimal('subtotal', 10, 2)->nullable();
            $table->string('discount_type')->nullable();
            $table->integer('discount_amount')->nullable();
            $table->decimal('calculated_discount_amount', 6, 2)->nullable();
            $table->enum('order_status', ['pending', 'invoiced', 'delivered'])->default('invoiced');
            $table->longText('other_charges')->nullable()->comment('JSON field for other charges');
            $table->text('other_charge_type')->nullable();
            $table->decimal('other_charge_percentage', 6, 2)->nullable();
            $table->decimal('other_charge_amount', 10, 2)->nullable();
            $table->decimal('round_off_from_total', 10, 2)->nullable();
            $table->unsignedFloat('decimal_round_off')->nullable();
            $table->decimal('total', 10, 2)->nullable();
            $table->longText('payments')->nullable()->comment('JSON field for payment methods');
            $table->unsignedFloat('paid_amount')->nullable();
            $table->unsignedFloat('due_amount')->nullable();
            $table->text('note')->nullable();
            $table->string('reference', 192)->nullable();
            $table->unsignedTinyInteger('is_returned')->default(0);
            $table->text('return_ids')->nullable();
            $table->string('buying_from', 20)->nullable();
            $table->unsignedBigInteger('creator')->nullable();
            $table->string('slug')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->json('request_data')->nullable()->comment('JSON field for request data');
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
        Schema::dropIfExists('product_orders');
    }
}

