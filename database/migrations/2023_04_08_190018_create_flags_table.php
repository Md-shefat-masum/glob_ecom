<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFlagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flags', function (Blueprint $table) {
            $table->id();
            $table->string('icon')->nullable();
            $table->string('name');
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('featured')->default(0)->comment("0=>Not Featured; 1=>Featured");
            $table->string('slug')->nullable();
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
        Schema::dropIfExists('flags');
    }
}
