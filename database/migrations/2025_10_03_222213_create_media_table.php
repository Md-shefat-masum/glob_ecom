<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('disk', 20)->default('public');
            $table->text('path')->nullable();

            $table->string('filename', 191)->nullable();
            $table->string('extension', 5)->nullable();
            $table->string('mime_type', 50)->nullable();
            $table->integer('size')->unsigned()->nullable();

            $table->json('folders')->nullable();
            $table->bigInteger('media_folder_id')->nullable();

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
        Schema::dropIfExists('media');
    }
}
