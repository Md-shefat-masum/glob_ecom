<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductSupplierContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_supplier_contacts', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('product_supplier_id')->nullable();
            $table->string('contact_number')->nullable();

            $table->unsignedBigInteger('creator')->nullable();
            $table->string('slug',)->nullable();
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
        Schema::dropIfExists('product_supplier_contacts');
    }
}
