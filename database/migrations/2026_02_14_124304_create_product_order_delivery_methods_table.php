<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateProductOrderDeliveryMethodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_order_delivery_methods', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('description')->nullable();

            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedBigInteger('creator')->nullable();
            $table->string('slug',)->nullable();
            $table->timestamps();
        });

        // Insert initial delivery methods: Home Delivery and Store Pickup
        DB::table('product_order_delivery_methods')->insert([
            [
                'title' => 'Home Delivery',
                'description' => 'Deliver product to the customer\'s home address',
                'status' => 'active',
                'creator' => null,
                'slug' => 'home-delivery',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Store Pickup',
                'description' => 'Customer will pick up the product from the store',
                'status' => 'active',
                'creator' => null,
                'slug' => 'store-pickup',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_order_delivery_methods');
    }
}
