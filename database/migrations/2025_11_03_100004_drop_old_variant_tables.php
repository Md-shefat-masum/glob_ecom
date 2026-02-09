<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropOldVariantTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop the old variant tables if they exist
        Schema::dropIfExists('product_stock_variants');
        Schema::dropIfExists('product_filter_variants');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // We won't recreate the old tables in down method
        // as we're moving to the new structure
    }
}

