<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDbSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('db_suppliers', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('store_id')->nullable();            
            $table->unsignedBigInteger('count_id')->nullable();                                                                       
            $table->string('supplier_code', 100)->nullable();
            $table->string('supplier_name', 100)->nullable();
            $table->string('mobile', 40)->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('gstin', 100)->nullable();
            $table->string('tax_number', 100)->nullable();
            $table->string('vatin', 150)->nullable();
            $table->double('opening_balance', 20, 4)->nullable();              
            $table->double('purchase_due', 20, 4)->nullable();              
            $table->double('purchase_return_due', 20, 4)->nullable();             
            $table->unsignedBigInteger('country_id')->nullable();    
            $table->unsignedBigInteger('state_id')->nullable();    
            $table->string('city', 150)->nullable();
            $table->string('postcode', 20)->nullable();
            $table->text('address')->nullable(); 
            $table->string('system_ip', 100)->nullable();   
            $table->string('system_name', 100)->nullable();   

            // $table->string('created_by', 50)->nullable();
            // $table->date('created_date')->nullable();   
            $table->string('created_time', 100)->nullable(); 
            $table->unsignedBigInteger('company_id')->nullable();    
            
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
        Schema::dropIfExists('db_suppliers');
    }
}
