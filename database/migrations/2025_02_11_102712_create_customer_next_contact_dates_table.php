<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerNextContactDatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_next_contact_dates', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('customer_id')->nullable();
            $table->date('next_date')->nullable();                       
            $table->enum('contact_status', ['pending', 'missed', 'done'])->default('pending');
            
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
        Schema::dropIfExists('customer_next_contact_dates');
    }
}
