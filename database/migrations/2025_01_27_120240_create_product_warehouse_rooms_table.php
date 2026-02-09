<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductWarehouseRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_warehouse_rooms', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('product_warehouse_id')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('code')->nullable()->unique();
            $table->string('image')->nullable();

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
        Schema::dropIfExists('product_warehouse_rooms');
    }
}
