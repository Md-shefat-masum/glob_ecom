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
            if (!Schema::hasColumn('products', 'notification_title')) {
                $table->string('notification_title')->nullable()->after('related_addon_products');
            }
            if (!Schema::hasColumn('products', 'notification_description')) {
                $table->text('notification_description')->nullable()->after('notification_title');
            }
            if (!Schema::hasColumn('products', 'notification_button_text')) {
                $table->string('notification_button_text', 150)->nullable()->after('notification_description');
            }
            if (!Schema::hasColumn('products', 'notification_button_url')) {
                $table->string('notification_button_url')->nullable()->after('notification_button_text');
            }
            if (!Schema::hasColumn('products', 'notification_image_path')) {
                $table->string('notification_image_path')->nullable()->after('notification_button_url');
            }
            if (!Schema::hasColumn('products', 'notification_image_id')) {
                $table->unsignedBigInteger('notification_image_id')->nullable()->after('notification_image_path');
            }
            if (!Schema::hasColumn('products', 'notification_is_show')) {
                $table->boolean('notification_is_show')->default(false)->after('notification_image_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'notification_is_show')) {
                $table->dropColumn('notification_is_show');
            }
            if (Schema::hasColumn('products', 'notification_image_id')) {
                $table->dropColumn('notification_image_id');
            }
            if (Schema::hasColumn('products', 'notification_image_path')) {
                $table->dropColumn('notification_image_path');
            }
            if (Schema::hasColumn('products', 'notification_button_url')) {
                $table->dropColumn('notification_button_url');
            }
            if (Schema::hasColumn('products', 'notification_button_text')) {
                $table->dropColumn('notification_button_text');
            }
            if (Schema::hasColumn('products', 'notification_description')) {
                $table->dropColumn('notification_description');
            }
            if (Schema::hasColumn('products', 'notification_title')) {
                $table->dropColumn('notification_title');
            }
        });
    }
};

