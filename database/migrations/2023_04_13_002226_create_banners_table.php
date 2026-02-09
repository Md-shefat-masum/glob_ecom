<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('type')->comment('1=>Sliders; 2=>Banners')->default(1);
            $table->string('image');
            $table->string('link')->nullable();
            $table->string('position')->nullable();


            // content
            $table->string('sub_title')->nullable();
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->string('btn_text')->nullable();
            $table->string('btn_link')->nullable();
            $table->string('text_position')->nullable();

            $table->string('slug');
            $table->tinyInteger('status')->default(1);
            $table->double('serial')->default(1);
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
        Schema::dropIfExists('banners');
    }
}
