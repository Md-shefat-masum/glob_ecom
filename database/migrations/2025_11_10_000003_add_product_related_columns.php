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
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'related_similar_products')) {
                $table->json('related_similar_products')->nullable()->after('availability_status');
            }
            if (!Schema::hasColumn('products', 'related_recommended_products')) {
                $table->json('related_recommended_products')->nullable()->after('related_similar_products');
            }
            if (!Schema::hasColumn('products', 'related_addon_products')) {
                $table->json('related_addon_products')->nullable()->after('related_recommended_products');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'related_addon_products')) {
                $table->dropColumn('related_addon_products');
            }
            if (Schema::hasColumn('products', 'related_recommended_products')) {
                $table->dropColumn('related_recommended_products');
            }
            if (Schema::hasColumn('products', 'related_similar_products')) {
                $table->dropColumn('related_similar_products');
            }
        });
    }
};

