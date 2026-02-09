<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManualProductReturnItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manual_product_return_items', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('manual_product_return_id');
            $table->unsignedBigInteger('product_id')->nullable()->comment('If product exists in system');
            $table->string('product_name');
            $table->unsignedInteger('qty');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            
            $table->string('slug')->nullable();
            $table->unsignedBigInteger('creator')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            // Indexes
            $table->index('manual_product_return_id');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('manual_product_return_items');
    }
}

