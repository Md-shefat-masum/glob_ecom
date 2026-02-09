<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('slug');
            $table->tinyInteger('status')->default(1)->comment("1=>Active; 0=>Inactive");
            $table->tinyInteger('featured')->default(0)->comment("0=>Not Featured; 1=>Featured");
            $table->tinyInteger('show_on_navbar')->default(1)->comment("1=>Yes; 0=>No");
            $table->tinyInteger('serial')->default(1);
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
        Schema::dropIfExists('categories');
    }
}
