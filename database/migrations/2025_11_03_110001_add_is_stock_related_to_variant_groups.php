<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddIsStockRelatedToVariantGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_stock_variant_groups', function (Blueprint $table) {
            $table->tinyInteger('is_stock_related')->default(1)->after('is_fixed')
                ->comment('1=>Stock-related (creates combinations); 0=>Filter-related (for frontend only)');
        });

        // Update existing groups
        // Stock-related groups (create combinations)
        DB::table('product_stock_variant_groups')->whereIn('slug', [
            'color', 'size', 'material', 'weight', 'storage', 'ram', 
            'screen_size', 'processor', 'connectivity', 'warranty'
        ])->update(['is_stock_related' => 1]);

        // Filter-related groups (frontend filtering only)
        DB::table('product_stock_variant_groups')->whereIn('slug', [
            'pattern', 'fit', 'sleeve_length', 'neck_type', 'type', 
            'origin', 'grade', 'freshness', 'pack_size'
        ])->update(['is_stock_related' => 0]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_stock_variant_groups', function (Blueprint $table) {
            $table->dropColumn('is_stock_related');
        });
    }
}

