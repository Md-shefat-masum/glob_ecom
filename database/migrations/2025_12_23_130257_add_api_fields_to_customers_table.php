<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApiFieldsToCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            // Add API fields
            $table->string('full_name')->nullable()->after('name');
            $table->string('phone_original', 60)->nullable()->after('phone');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('email');
            $table->string('thana')->nullable()->after('address');
            $table->string('post_code', 20)->nullable()->after('thana');
            $table->string('city')->nullable()->after('post_code');
            $table->string('country')->nullable()->after('city');
            $table->string('order_id')->nullable()->after('country');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'full_name',
                'phone_original',
                'gender',
                'thana',
                'post_code',
                'city',
                'country',
                'order_id'
            ]);
        });
    }
}
