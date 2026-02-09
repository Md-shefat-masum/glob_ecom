<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDbSupplierPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('db_supplier_payments', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('purchasepayment_id')->nullable();            
            $table->unsignedBigInteger('supplier_id')->nullable();            
            $table->date('payment_date')->nullable();                   
            $table->string('payment_type', 100)->nullable();
            $table->double('payment', 20, 4)->nullable();              
            $table->text('payment_note')->nullable(); 

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
        Schema::dropIfExists('db_supplier_payments');
    }
}
