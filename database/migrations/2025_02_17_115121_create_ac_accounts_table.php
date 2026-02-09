<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAcAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ac_accounts', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('count_id')->nullable();                  
            $table->unsignedBigInteger('store_id')->nullable();            
            $table->unsignedBigInteger('parent_id')->nullable()->default(0);
            
            // Account Classification
            $table->enum('account_type', ['asset', 'liability', 'equity', 'revenue', 'expense'])->nullable();
            $table->enum('normal_balance', ['debit', 'credit'])->nullable();
            $table->boolean('is_system_account')->default(false);
            $table->boolean('is_control_account')->default(false);
            
            $table->string('sort_code', 100)->nullable();
            $table->string('account_name', 100)->nullable();
            $table->string('account_code', 100)->nullable();
            $table->string('short_code', 100)->nullable();    
            $table->double('balance', 20, 4)->default(0);              
            $table->text('note')->nullable(); 
            
            // $table->string('created_by', 50)->nullable();
            // $table->date('created_date')->nullable();   
            $table->string('created_time', 50)->nullable();   
            $table->string('system_ip', 50)->nullable();   
            $table->string('system_name', 50)->nullable();   

            $table->unsignedBigInteger('delete_bit')->nullable()->default(0);
            $table->string('account_selection_name', 50)->nullable();   

            // Foreign Keys
            $table->unsignedBigInteger('paymenttypes_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->unsignedBigInteger('expense_id')->nullable();

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
        Schema::dropIfExists('ac_accounts');
    }
}
