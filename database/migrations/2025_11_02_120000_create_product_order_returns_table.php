<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductOrderReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_order_returns', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('product_order_id')->nullable();
            $table->string('return_code', 100)->nullable();
            
            $table->unsignedBigInteger('product_warehouse_id')->nullable();
            $table->unsignedBigInteger('product_warehouse_room_id')->nullable();
            $table->unsignedBigInteger('product_warehouse_room_cartoon_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            
            $table->date('return_date')->nullable();
            $table->text('return_reason')->nullable();
            
            $table->json('other_charges')->nullable();
            $table->decimal('other_charge_amount', 10, 2)->nullable();
            
            $table->string('discount_type', 50)->nullable();
            $table->decimal('discount_amount', 10, 2)->nullable();
            $table->decimal('calculated_discount_amount', 10, 2)->nullable();
            
            $table->decimal('round_off_from_total', 10, 2)->nullable();
            $table->decimal('decimal_round_off', 10, 2)->nullable();
            
            $table->decimal('subtotal', 10, 2)->nullable();
            $table->decimal('total', 10, 2)->nullable();
            
            $table->enum('refund_method', ['cash', 'bkash', 'rocket', 'nogod', 'bank', 'cheque', 'advance_payment'])->default('advance_payment');
            $table->enum('refund_status', ['pending', 'completed'])->default('pending');
            
            $table->text('note')->nullable();
            $table->enum('return_status', ['pending', 'approved', 'rejected'])->default('approved');
            
            $table->unsignedBigInteger('creator')->nullable();
            $table->string('slug')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            // Add indexes for better query performance
            $table->index('product_order_id');
            $table->index('customer_id');
            $table->index('return_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_order_returns');
    }
}

