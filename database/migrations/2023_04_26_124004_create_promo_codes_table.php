<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromoCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();
            $table->string('icon')->nullable();
            $table->string('title')->nullable();
            $table->longText('description')->nullable();
            $table->string('code');
            $table->date('effective_date');
            $table->date('expire_date');
            $table->tinyInteger('type')->comment("1=>Amount; 2=>Percentage");
            $table->double('value')->default(0);
            $table->double('minimum_order_amount')->nullable();
            $table->string('slug');
            $table->tinyInteger('status')->default(1)->comment("1=>Active; 0=>Inactive");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promo_codes');
    }
}
