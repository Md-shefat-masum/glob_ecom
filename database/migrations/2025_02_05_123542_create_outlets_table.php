<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outlets', function (Blueprint $table) {
            $table->id();

            $table->string('title')->nullable();
            $table->string('image')->nullable();
            $table->string('address')->nullable();
            $table->text('description')->nullable();
            $table->string('opening')->nullable();
            $table->string('contact_number_1', 60)->nullable();
            $table->string('contact_number_2', 60)->nullable();
            $table->string('contact_number_3', 60)->nullable();
            $table->text('map')->nullable();

            $table->unsignedBigInteger('creator')->nullable();
            $table->string('slug',)->nullable();
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
        Schema::dropIfExists('outlets');
    }
}
