<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no')->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->dateTime('order_date');
            $table->date('estimated_dd')->nullable();
            $table->dateTime('delivery_date')->nullable();
            $table->string('delivery_method')->comment('1=>Home Delivery; 2=>Store Pickup')->nullable();
            $table->tinyInteger('payment_method')->nullable()->comment('1=>cash_on_delivery; 2=>bkash; 3=>nagad; 4=>Card');
            $table->tinyInteger('payment_status')->nullable()->comment('0=>Unpaid; 1=>Payment Success; 2=>Payment Failed');
            $table->string('trx_id')->nullable()->comment("Created By GenericCommerceV1");
            $table->string('bank_tran_id')->nullable()->comment("KEEP THIS bank_tran_id FOR REFUNDING ISSUE");
            $table->tinyInteger('order_status')->default(0)->comment('0=>pending/processing; 1=>confirmed; 2=>intransit; 3=>delivered; 4=>cancel');
            $table->double('sub_total')->default(0);
            $table->string('coupon_code')->nullable();
            $table->double('discount')->default(0);
            $table->double('delivery_fee')->default(0);
            $table->double('vat')->default(0);
            $table->double('tax')->default(0);
            $table->double('total')->default(0);
            $table->longText('order_note')->comment("Order Note By Customer")->nullable();
            $table->longText('order_remarks')->comment("Special Note By Admin")->nullable();
            $table->string('slug')->unique();
            $table->tinyInteger('complete_order')->default(0)->comment('0=>Incomplete Order (Address Missing); 1=>Complete Order (Address Given)');
            $table->softDeletes();
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
        Schema::dropIfExists('orders');
    }
}
