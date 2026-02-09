<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductUnitPricingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_unit_pricing', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('unit_id');
            $table->string('unit_title', 50)->nullable(); // e.g., "gm", "kg", "box"
            $table->double('unit_value')->default(1); // e.g., 650 for 650gm
            $table->string('unit_label', 50)->nullable(); // e.g., "650 gm", "3 kg"
            $table->double('price')->default(0);
            $table->double('discount_price')->default(0);
            $table->integer('discount_percent')->default(0);
            $table->double('reward_points')->default(0);
            $table->tinyInteger('is_default')->default(0)->comment('1=>Default pricing; 0=>Not default');
            $table->tinyInteger('status')->default(1)->comment('1=>Active; 0=>Inactive');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_unit_pricing');
    }
}

