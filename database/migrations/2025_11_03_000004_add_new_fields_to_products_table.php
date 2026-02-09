<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewFieldsToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // Check if columns don't exist before adding
            if (!Schema::hasColumn('products', 'sku')) {
                $table->string('sku', 100)->nullable()->after('code');
            }
            if (!Schema::hasColumn('products', 'barcode')) {
                $table->string('barcode', 100)->nullable()->after('sku');
            }
            if (!Schema::hasColumn('products', 'hsn_code')) {
                $table->string('hsn_code', 100)->nullable()->after('barcode');
            }
            if (!Schema::hasColumn('products', 'meta_image')) {
                $table->string('meta_image')->nullable()->after('meta_description');
            }
            if (!Schema::hasColumn('products', 'min_order_qty')) {
                $table->integer('min_order_qty')->default(1)->after('stock');
            }
            if (!Schema::hasColumn('products', 'max_order_qty')) {
                $table->integer('max_order_qty')->nullable()->after('min_order_qty');
            }
            if (!Schema::hasColumn('products', 'attributes')) {
                $table->json('attributes')->nullable()->after('specification')->comment('Product attributes like material, style, pattern, dimensions, measurements');
            }
            if (!Schema::hasColumn('products', 'shipping_info')) {
                $table->json('shipping_info')->nullable()->after('attributes')->comment('Shipping information: weight, dimensions, package type, returnable');
            }
            if (!Schema::hasColumn('products', 'tax_info')) {
                $table->json('tax_info')->nullable()->after('shipping_info')->comment('Tax information: tax_class_id, tax_percent');
            }
            if (!Schema::hasColumn('products', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('is_product_qty_multiply');
            }
            if (!Schema::hasColumn('products', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $columns = ['sku', 'barcode', 'hsn_code', 'meta_image', 'min_order_qty', 'max_order_qty', 
                       'attributes', 'shipping_info', 'tax_info', 'created_by', 'updated_by'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}

