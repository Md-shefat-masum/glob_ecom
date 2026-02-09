<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('customer_category_id')->nullable();
            $table->unsignedBigInteger('customer_source_type_id')->nullable();
            $table->unsignedBigInteger('reference_by')->nullable();
            $table->string('name')->nullable();
            $table->string('phone', 60)->nullable();
            $table->string('email', 100)->nullable();            
            $table->text('address')->nullable();
            
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
        Schema::dropIfExists('customers');
    }
}
