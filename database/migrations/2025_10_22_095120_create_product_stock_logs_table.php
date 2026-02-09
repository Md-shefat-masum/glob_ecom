<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductStockLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_stock_logs', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('warehouse_id')->unsigned()->nullable();
            $table->bigInteger('product_id')->unsigned()->nullable();
            $table->string('product_name')->nullable();

            $table->bigInteger('product_sales_id')->unsigned()->nullable();
            $table->bigInteger('product_purchase_id')->unsigned()->nullable();
            $table->bigInteger('product_return_id')->unsigned()->nullable();

            $table->integer('quantity')->nullable();
            $table->enum(
                'type',
                [
                    'sales',
                    'purchase',
                    'purchase_return',
                    'return',
                    'initial',
                    'transfer',
                    'waste'
                ]
            )
                ->nullable();

            $table->bigInteger('creator')->unsigned()->nullable();
            $table->string('slug', 50)->nullable();
            $table->tinyInteger('status')->unsigned()->default(1);

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
        Schema::dropIfExists('product_stock_logs');
    }
}
