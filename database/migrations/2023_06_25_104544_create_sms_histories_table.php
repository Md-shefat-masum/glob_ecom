<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template_id')->nullable();
            $table->string('template_title')->nullable();
            $table->longText('template_description')->nullable();
            $table->tinyInteger('sending_type')->nullable()->comment("1=>Individual; 2=>Everyone");
            $table->string('individual_contact')->nullable();
            $table->tinyInteger('sms_receivers')->nullable()->comment("1=>Having No Order; 2=>Having Orders");
            $table->double('min_order')->nullable();
            $table->double('max_order')->nullable();
            $table->double('min_order_value')->nullable();
            $table->double('max_order_value')->nullable();
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
        Schema::dropIfExists('sms_histories');
    }
}
