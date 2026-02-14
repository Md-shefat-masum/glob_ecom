<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateProductOrderCourierMethodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_order_courier_methods', function (Blueprint $table) {
            $table->id();

            $table->string('title', 50)->nullable();
            $table->json('config')->nullable();

            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedBigInteger('creator')->nullable();
            $table->string('slug',)->nullable();
            $table->timestamps();
        });

        DB::table('product_order_courier_methods')->insert([
            'title' => 'Pathao',
            'config' => json_encode([
                'client_id' => env('PATHAO_CLIENT_ID', ''),
                'client_secret' => env('PATHAO_CLIENT_SECRET', ''),
                'grant_type' => 'password',
                'username' => env('PATHAO_USERNAME', ''),
                'password' => env('PATHAO_PASSWORD', ''),
            ]),
            'status' => 'active',
            'creator' => 1,
        ]);

        DB::table('product_order_courier_methods')->insert([
            'title' => 'Steadfast',
            'config' => json_encode([
                'api_key' => env('STEADFAST_API_KEY', ''),
                'api_secret' => env('STEADFAST_API_SECRET', ''),
                'api_url' => env('STEADFAST_API_URL', ''),
            ]),
            'status' => 'active',
            'creator' => 1,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_order_courier_methods');
    }
}
