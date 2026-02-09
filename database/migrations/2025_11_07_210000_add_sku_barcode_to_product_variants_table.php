<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            if (!Schema::hasColumn('product_variants', 'sku')) {
                $table->string('sku')->nullable()->after('discounted_price');
            }

            if (!Schema::hasColumn('product_variants', 'barcode')) {
                $table->string('barcode')->nullable()->after('sku');
            }
        });
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            if (Schema::hasColumn('product_variants', 'barcode')) {
                $table->dropColumn('barcode');
            }

            if (Schema::hasColumn('product_variants', 'sku')) {
                $table->dropColumn('sku');
            }
        });
    }
};
