<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaInUsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media_in_uses', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('media_id')->unsigned()->nullable();
            $table->string('model',100)->nullable();
            $table->bigInteger('model_id')->unsigned()->nullable();
            $table->string('col_name',100)->nullable();

            $table->bigInteger('creator')->unsigned()->nullable();
            $table->string('slug',50)->nullable();
            $table->tinyInteger('status')->unsigned()->default(1);
            
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
        Schema::dropIfExists('media_in_uses');
    }
}
