<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSideBannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('side_banners', function (Blueprint $table) {
            $table->id();
            $table->string('banner_img')->nullable();
            $table->string('banner_link')->nullable();
            $table->string('title')->nullable();
            $table->string('button_title')->nullable();
            $table->string('button_url')->nullable();
            
            $table->unsignedBigInteger('creator')->nullable();
            $table->string('slug')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
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
        Schema::dropIfExists('side_banners');
    }
}
