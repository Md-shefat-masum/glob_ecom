<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDbTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('db_taxes', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('store_id')->nullable();            
            $table->string('tax_name', 100)->nullable();
            $table->double('tax', 20, 4)->nullable();              
            $table->integer('group_bit')->nullable();
            $table->string('subtax_ids', 50)->nullable();   


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
        Schema::dropIfExists('db_taxes');
    }
}
