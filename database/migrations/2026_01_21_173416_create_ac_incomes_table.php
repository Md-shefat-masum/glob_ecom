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
        Schema::create('ac_incomes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('store_id')->nullable();
            $table->bigInteger('count_id')->nullable()->comment('Use to create Income Code');
            $table->string('code', 100)->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->date('date')->nullable();
            $table->string('reference', 255)->nullable();
            $table->string('income_for', 255)->nullable();
            $table->double('amount', 20, 4)->nullable();
            $table->text('note')->nullable();
            $table->unsignedBigInteger('debit_account_id')->nullable();
            $table->unsignedBigInteger('credit_account_id')->nullable();
            $table->string('created_by', 100)->nullable();
            $table->date('created_date')->nullable();
            $table->string('created_time', 100)->nullable();
            $table->string('system_ip', 100)->nullable();
            $table->string('system_name', 100)->nullable();
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
        Schema::dropIfExists('ac_incomes');
    }
};
