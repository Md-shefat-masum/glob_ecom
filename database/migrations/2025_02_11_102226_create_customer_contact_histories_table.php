<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerContactHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_contact_histories', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->date('date')->nullable();           
            $table->text('note')->nullable();
            $table->enum('contact_history_status', ['planned', 'held', 'not_held'])->default('planned');
            $table->enum('priority', ['low', 'normal', 'medium', 'high', 'urgent', 'immediate'])->default('normal');
            
            
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
        Schema::dropIfExists('customer_contact_histories');
    }
}
