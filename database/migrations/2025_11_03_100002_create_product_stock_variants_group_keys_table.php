<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductStockVariantsGroupKeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_stock_variants_group_keys', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id'); // FK to product_stock_variant_groups
            $table->string('key_name', 100); // Red, SM, Cotton, 50kg, etc.
            $table->string('key_value')->nullable(); // Color code, size value, etc.
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(0);
            $table->tinyInteger('status')->default(1)->comment('1=>Active; 0=>Inactive');
            $table->timestamps();

            $table->foreign('group_id')->references('id')->on('product_stock_variant_groups')->onDelete('cascade');
            $table->index(['group_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_stock_variants_group_keys');
    }
}

