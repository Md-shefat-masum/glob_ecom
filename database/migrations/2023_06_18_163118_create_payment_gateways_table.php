<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentGatewaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('provider_name');
            $table->string('api_key')->nullable()->comment("StoreID/ApiKey");
            $table->string('secret_key')->nullable()->comment("StorePassword/SecretKey");
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->tinyInteger('live')->default(1)->comment("0=>Test/Sandbox; 1=>Product/Live");
            $table->tinyInteger('status')->default(1)->comment("0=>Inactive; 1=>Active");
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
        Schema::dropIfExists('payment_gateways');
    }
}
