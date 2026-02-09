<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductFieldsToCustomerContactHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_contact_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->nullable()->after('employee_id');
            $table->string('product_name')->nullable()->after('product_id');
            $table->text('subject')->nullable()->after('product_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_contact_histories', function (Blueprint $table) {
            $table->dropColumn(['product_id', 'product_name', 'subject']);
        });
    }
}
