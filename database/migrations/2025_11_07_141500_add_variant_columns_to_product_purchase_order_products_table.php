<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('product_purchase_order_products', function (Blueprint $table) {
            if (!Schema::hasColumn('product_purchase_order_products', 'variant_combination_id')) {
                $table->unsignedBigInteger('variant_combination_id')->nullable()->after('product_id');
            }

            if (!Schema::hasColumn('product_purchase_order_products', 'previous_stock')) {
                $table->decimal('previous_stock', 12, 3)->nullable()->after('qty');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_purchase_order_products', function (Blueprint $table) {
            if (Schema::hasColumn('product_purchase_order_products', 'previous_stock')) {
                $table->dropColumn('previous_stock');
            }

            if (Schema::hasColumn('product_purchase_order_products', 'variant_combination_id')) {
                $table->dropColumn('variant_combination_id');
            }
        });
    }
};

