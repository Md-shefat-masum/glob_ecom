<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarehouseToProductOrderHold extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('product_order_hold') && !Schema::hasColumn('product_order_hold', 'product_warehouse_id')) {
            Schema::table('product_order_hold', function (Blueprint $table) {
                $table->unsignedBigInteger('product_warehouse_id')->nullable()->after('customer_id')->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('product_order_hold') && Schema::hasColumn('product_order_hold', 'product_warehouse_id')) {
            Schema::table('product_order_hold', function (Blueprint $table) {
                $table->dropColumn('product_warehouse_id');
            });
        }
    }
}


