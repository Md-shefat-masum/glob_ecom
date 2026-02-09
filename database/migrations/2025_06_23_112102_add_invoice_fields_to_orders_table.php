<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInvoiceFieldsToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('invoice_no')->nullable()->after('order_no');
            $table->timestamp('invoice_date')->nullable()->after('order_date');
            $table->boolean('invoice_generated')->default(0)->after('complete_order');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['invoice_no', 'invoice_date', 'invoice_generated']);
        });
    }
}
