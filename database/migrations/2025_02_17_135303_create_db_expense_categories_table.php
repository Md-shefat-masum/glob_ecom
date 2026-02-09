<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDbExpenseCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('db_expense_categories', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('store_id')->nullable();              
            $table->string('category_code', 100)->nullable();
            $table->string('category_name', 100)->nullable();
            $table->text('description')->nullable(); 

            // $table->string('created_by', 50)->nullable();
            // $table->date('created_date')->nullable();
            
            // $table->string('created_time', 100)->nullable();  
            // $table->string('system_ip', 100)->nullable();  
            // $table->string('system_name', 100)->nullable();              
            
          
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
        Schema::dropIfExists('db_expense_categories');
    }
}
