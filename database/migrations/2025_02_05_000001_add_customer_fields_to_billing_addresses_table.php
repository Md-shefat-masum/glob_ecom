<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomerFieldsToBillingAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('billing_addresses', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->nullable()->after('order_id');
            $table->string('full_name')->nullable()->after('customer_id');
            $table->string('phone')->nullable()->after('full_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('billing_addresses', function (Blueprint $table) {
            $table->dropColumn(['customer_id', 'full_name', 'phone']);
        });
    }
}
