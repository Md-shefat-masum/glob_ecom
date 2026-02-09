<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductFilterVariantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_filter_variants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('group_name', 100); // e.g., "Color", "Size", "Material", "Style", "Pattern"
            $table->string('variant_title', 100); // e.g., "Red", "M", "Cotton"
            $table->string('variant_value')->nullable(); // e.g., "#FF0000" for color code, or numeric value
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(0);
            $table->tinyInteger('status')->default(1)->comment('1=>Active; 0=>Inactive');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->index(['product_id', 'group_name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_filter_variants');
    }
}

