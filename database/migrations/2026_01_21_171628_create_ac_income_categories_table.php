<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ac_income_categories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('store_id')->nullable();
            $table->string('code', 100)->nullable();
            $table->string('name', 100)->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('debit_id')->nullable();
            $table->unsignedBigInteger('credit_id')->nullable();
            $table->string('created_by', 100)->nullable();
            $table->date('created_date')->nullable();
            $table->bigInteger('creator')->nullable();
            $table->string('slug', 192)->nullable();
            $table->enum('status', ['active', 'inactive'])->nullable();
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
        Schema::dropIfExists('ac_income_categories');
    }
};
