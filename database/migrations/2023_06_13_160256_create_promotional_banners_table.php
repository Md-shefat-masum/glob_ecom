<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromotionalBannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotional_banners', function (Blueprint $table) {
            $table->id();
            $table->string('icon')->nullable();
            $table->string('heading')->nullable();
            $table->string('heading_color')->nullable();
            $table->string('title')->nullable();
            $table->string('title_color')->nullable();
            $table->string('description')->nullable();
            $table->string('description_color')->nullable();

            $table->string('url')->nullable();
            $table->string('btn_text')->nullable();
            $table->string('btn_text_color')->nullable();
            $table->string('btn_bg_color')->nullable();

            $table->string('background_color')->nullable();
            $table->string('product_image')->nullable();
            $table->string('background_image')->nullable();
            $table->string('video_url')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('end_at')->nullable();
            $table->string('time_bg_color')->nullable();
            $table->string('time_font_color')->nullable();
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
        Schema::dropIfExists('promotional_banners');
    }
}
