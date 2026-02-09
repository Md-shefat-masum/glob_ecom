<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderProgressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_progress', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->tinyInteger('order_status')->default(0)->comment('0=>pending/processing; 1=>confirmed; 2=>intransit; 3=>delivered; 4=>cancel');
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
        Schema::dropIfExists('order_progress');
    }
}
