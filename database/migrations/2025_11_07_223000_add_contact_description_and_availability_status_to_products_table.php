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
            if (!Schema::hasColumn('products', 'contact_description')) {
                $table->text('contact_description')->nullable()->after('contact_number');
            }

            if (!Schema::hasColumn('products', 'availability_status')) {
                $table->string('availability_status', 50)->default('in_stock')->after('contact_description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'availability_status')) {
                $table->dropColumn('availability_status');
            }

            if (Schema::hasColumn('products', 'contact_description')) {
                $table->dropColumn('contact_description');
            }
        });
    }
};

