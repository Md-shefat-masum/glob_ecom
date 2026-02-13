<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentModeTitleToDbCustomerPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('db_customer_payments', function (Blueprint $table) {
            // Then, add payment_mode_title after payment_mode
            if (!Schema::hasColumn('db_customer_payments', 'payment_mode_title')) {
                $table->string('payment_mode_title', 100)->nullable()->after('payment_mode')->comment('Payment mode title/name for display purposes');
            }
        });

        Schema::table('ac_transactions', function (Blueprint $table) {
            // Then, add ref_customer_payment_id after ref_customer_payment_id
            if (!Schema::hasColumn('ac_transactions', 'ref_customer_payment_id')) {
                $table->unsignedBigInteger('ref_customer_payment_id')->nullable()->after('ref_sales_id')->comment('Reference to db_customer_payments table');
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
            if (Schema::hasColumn('db_customer_payments', 'payment_mode_title')) {
                $table->dropColumn('payment_mode_title');
            }
        });

        Schema::table('ac_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('ac_transactions', 'ref_customer_payment_id')) {
                $table->dropColumn('ref_customer_payment_id');
            }
        });
    }
}
