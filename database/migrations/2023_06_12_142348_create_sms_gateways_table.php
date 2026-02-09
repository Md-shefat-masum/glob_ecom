<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsGatewaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable();
            $table->string('provider_name');
            $table->string('api_endpoint');
            $table->string('api_key')->nullable();
            $table->string('secret_key')->nullable();
            $table->string('sender_id')->nullable();
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
        Schema::dropIfExists('sms_gateways');
    }
}
