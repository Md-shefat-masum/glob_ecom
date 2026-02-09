<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('logo')->nullable();
            $table->string('banner')->nullable();
            $table->string('categories')->nullable();
            $table->string('subcategories')->nullable();
            $table->tinyInteger('featured')->default(0)->comment("0=> Not Featured; 1=> Featured");
            $table->tinyInteger('status')->default(1)->comment("0=> Inactive; 1=> Active");
            $table->tinyInteger('serial')->default(1);
            $table->string('slug');
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
        Schema::dropIfExists('brands');
    }
}
