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
        Schema::table('product_variant_combinations', function (Blueprint $table) {
            if (!Schema::hasColumn('product_variant_combinations', 'product_warehouse_id')) {
                $table->unsignedBigInteger('product_warehouse_id')->nullable()->after('stock');
            }
            if (!Schema::hasColumn('product_variant_combinations', 'product_warehouse_room_id')) {
                $table->unsignedBigInteger('product_warehouse_room_id')->nullable()->after('product_warehouse_id');
            }
            if (!Schema::hasColumn('product_variant_combinations', 'product_warehouse_room_cartoon_id')) {
                $table->unsignedBigInteger('product_warehouse_room_cartoon_id')->nullable()->after('product_warehouse_room_id');
            }
        });

        Schema::table('product_stocks', function (Blueprint $table) {
            if (!Schema::hasColumn('product_stocks', 'variant_combination_id')) {
                $table->unsignedBigInteger('variant_combination_id')->nullable()->after('product_id');
            }
        });

        Schema::table('product_stock_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('product_stock_logs', 'variant_combination_id')) {
                $table->unsignedBigInteger('variant_combination_id')->nullable()->after('product_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variant_combinations', function (Blueprint $table) {
            if (Schema::hasColumn('product_variant_combinations', 'product_warehouse_room_cartoon_id')) {
                $table->dropColumn('product_warehouse_room_cartoon_id');
            }
            if (Schema::hasColumn('product_variant_combinations', 'product_warehouse_room_id')) {
                $table->dropColumn('product_warehouse_room_id');
            }
            if (Schema::hasColumn('product_variant_combinations', 'product_warehouse_id')) {
                $table->dropColumn('product_warehouse_id');
            }
        });

        Schema::table('product_stocks', function (Blueprint $table) {
            if (Schema::hasColumn('product_stocks', 'variant_combination_id')) {
                $table->dropColumn('variant_combination_id');
            }
        });

        Schema::table('product_stock_logs', function (Blueprint $table) {
            if (Schema::hasColumn('product_stock_logs', 'variant_combination_id')) {
                $table->dropColumn('variant_combination_id');
            }
        });
    }
};


