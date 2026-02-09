<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAcMoneyDepositsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ac_money_deposits', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('store_id')->nullable();            
            $table->date('deposit_date')->nullable();                   
            $table->string('reference_no', 100)->nullable();
            $table->unsignedBigInteger('debit_account_id')->nullable();
            $table->unsignedBigInteger('credit_account_id')->nullable();
            $table->double('amount', 20, 4)->nullable();              
            $table->text('note')->nullable(); 

            // $table->string('created_by', 50)->nullable();
            // $table->date('created_date')->nullable();   

            $table->string('created_time', 100)->nullable();  
            $table->string('system_ip', 100)->nullable();  
            $table->string('system_name', 100)->nullable();              

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
        Schema::dropIfExists('ac_money_deposits');
    }
}
