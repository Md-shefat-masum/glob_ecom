<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_stocks', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('product_warehouse_id')->nullable();
            $table->unsignedBigInteger('product_warehouse_room_id')->nullable();
            $table->unsignedBigInteger('product_warehouse_room_cartoon_id')->nullable();
            $table->unsignedBigInteger('product_supplier_id')->nullable();
            $table->unsignedBigInteger('product_purchase_order_id')->nullable();             
            $table->unsignedBigInteger('product_id')->nullable();                
            $table->date('date')->nullable();          
            $table->unsignedMediumInteger('qty')->nullable();                                        
            $table->decimal('purchase_price', 10, 2)->nullable();

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
        Schema::dropIfExists('product_stocks');
    }
}
