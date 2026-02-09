<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVariantAndUnitPriceToProductOrderProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_order_products', function (Blueprint $table) {
            $table->unsignedBigInteger('variant_id')->nullable()->after('product_id')
                ->comment('Product variant combination ID if product has variants');
            $table->unsignedBigInteger('unit_price_id')->nullable()->after('variant_id')
                ->comment('Product unit pricing ID if product has unit-based pricing');
            
            // Add indexes for better query performance
            $table->index('variant_id');
            $table->index('unit_price_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_order_products', function (Blueprint $table) {
            $table->dropIndex(['variant_id']);
            $table->dropIndex(['unit_price_id']);
            $table->dropColumn(['variant_id', 'unit_price_id']);
        });
    }
}
