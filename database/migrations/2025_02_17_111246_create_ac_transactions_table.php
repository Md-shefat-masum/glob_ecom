<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAcTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ac_transactions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('store_id')->nullable();
            $table->string('payment_code', 100)->nullable();
            $table->date('transaction_date')->nullable();   
            $table->string('transaction_type', 200)->nullable();
            $table->string('event_type', 100)->nullable()->comment('References event_name from ac_event_mappings');
            $table->unsignedBigInteger('debit_account_id')->nullable();                  
            $table->unsignedBigInteger('credit_account_id')->nullable();                  
            $table->double('debit_amt', 20, 4)->nullable();  
            $table->double('credit_amt', 20, 4)->nullable();  
            $table->text('note')->nullable(); 

            $table->unsignedBigInteger('ref_accounts_id')->nullable();    
            $table->unsignedBigInteger('ref_moneytransfer_id')->nullable();    
            $table->unsignedBigInteger('ref_moneydeposits_id')->nullable();    
            $table->unsignedBigInteger('ref_salespayments_id')->nullable();    
            $table->unsignedBigInteger('ref_salespaymentsreturn_id')->nullable();    
            $table->unsignedBigInteger('ref_purchasepayments_id')->nullable();    
            $table->unsignedBigInteger('ref_purchasepaymentsreturn_id')->nullable();    
            $table->unsignedBigInteger('ref_expense_id')->nullable();    

            $table->unsignedBigInteger('supplier_id')->nullable();    
            $table->unsignedBigInteger('customer_id')->nullable();  
            $table->string('short_code', 100)->nullable();    
            
            // $table->string('created_by', 50)->nullable();
            // $table->date('created_date')->nullable();   

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
        Schema::dropIfExists('ac_transactions');
    }
}
