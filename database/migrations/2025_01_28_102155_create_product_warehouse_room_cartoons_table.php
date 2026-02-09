<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductWarehouseRoomCartoonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_warehouse_room_cartoons', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('product_warehouse_id')->nullable();
            $table->unsignedBigInteger('product_warehouse_room_id')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('code')->nullable()->unique();

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
        Schema::dropIfExists('product_warehouse_room_cartoons');
    }
}
