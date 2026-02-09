<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_pages', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable();
            $table->string('page_title')->nullable();
            $table->longText('description')->nullable();
            $table->string('slug');
            $table->tinyInteger('status')->default(1)->comment("1=>Active; 0=>Inactive");

            $table->string('meta_title')->nullable();
            $table->string('meta_keyword')->nullable();
            $table->longText('meta_description')->nullable();
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
        Schema::dropIfExists('custom_pages');
    }
}
