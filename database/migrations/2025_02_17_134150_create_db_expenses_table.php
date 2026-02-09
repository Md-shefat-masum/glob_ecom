<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDbExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('db_expenses', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('store_id')->nullable();            
            $table->unsignedBigInteger('count_id')->nullable();
            $table->string('expense_code', 100)->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->date('expense_date')->nullable();   
            $table->string('reference_no', 100)->nullable();
            $table->string('expense_for', 255)->nullable();
            $table->double('expense_amt', 20, 4)->nullable();              
            $table->unsignedBigInteger('payment_type_id')->nullable();
            $table->unsignedBigInteger('account_id')->nullable();    
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
        Schema::dropIfExists('db_expenses');
    }
}
