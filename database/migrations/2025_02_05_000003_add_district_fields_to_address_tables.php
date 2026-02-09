<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDistrictFieldsToAddressTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('billing_addresses', function (Blueprint $table) {
            $table->unsignedBigInteger('division_id')->nullable()->after('phone');
            $table->unsignedBigInteger('district_id')->nullable()->after('division_id');
        });
        
        Schema::table('shipping_addresses', function (Blueprint $table) {
            $table->unsignedBigInteger('division_id')->nullable()->after('phone');
            $table->unsignedBigInteger('district_id')->nullable()->after('division_id');
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
            $table->dropColumn(['division_id', 'district_id']);
        });
        
        Schema::table('shipping_addresses', function (Blueprint $table) {
            $table->dropColumn(['division_id', 'district_id']);
        });
    }
}
