<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductStockVariantGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_stock_variant_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100); // Color, Size, Material, Weight, etc.
            $table->string('slug', 100)->unique();
            $table->text('description')->nullable();
            $table->tinyInteger('is_fixed')->default(0)->comment('1=>Fixed (Color, Size); 0=>Dynamic');
            $table->integer('sort_order')->default(0);
            $table->tinyInteger('status')->default(1)->comment('1=>Active; 0=>Inactive');
            $table->timestamps();
        });

        // Insert fixed variant groups
        DB::table('product_stock_variant_groups')->insert([
            [
                'name' => 'Color',
                'slug' => 'color',
                'description' => 'Product color variants',
                'is_fixed' => 1,
                'sort_order' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Size',
                'slug' => 'size',
                'description' => 'Product size variants',
                'is_fixed' => 1,
                'sort_order' => 2,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_stock_variant_groups');
    }
}

