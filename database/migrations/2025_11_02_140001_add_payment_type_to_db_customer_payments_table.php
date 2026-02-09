<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentTypeToDbCustomerPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('db_customer_payments', function (Blueprint $table) {
            // Check if columns don't exist before adding
            if (!Schema::hasColumn('db_customer_payments', 'payment_mode')) {
                $table->string('payment_mode', 50)->nullable()->after('payment')->comment('cash, bkash, rocket, bank, etc');
            }
            
            if (!Schema::hasColumn('db_customer_payments', 'order_id')) {
                $table->unsignedBigInteger('order_id')->nullable()->after('orderpayment_id')->comment('Link to product_orders table');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('db_customer_payments', function (Blueprint $table) {
            if (Schema::hasColumn('db_customer_payments', 'payment_mode')) {
                $table->dropColumn('payment_mode');
            }
            if (Schema::hasColumn('db_customer_payments', 'order_id')) {
                $table->dropColumn('order_id');
            }
        });
    }
}

