<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('subcategory_id')->nullable();
            $table->unsignedBigInteger('childcategory_id')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('image');
            $table->string('multiple_images')->nullable();
            $table->longText('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->longText('specification')->nullable();
            $table->longText('warrenty_policy')->nullable();
            $table->double('price')->default(0);
            $table->double('discount_price')->default(0);
            $table->double('stock')->default(0);
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->string('tags')->nullable();
            $table->string('video_url')->nullable();
            $table->tinyInteger('warrenty_id')->nullable();
            $table->string('slug')->nullable();
            $table->tinyInteger('flag_id')->nullable();

            $table->string('meta_title')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('meta_description')->nullable();

            $table->tinyInteger('status')->default(1)->comment("1=>Active; 0=>Inactive");
            $table->tinyInteger('has_variant')->default(0)->comment("0=>No Variant; 1=>Product Has variant based on Colors, Region etc.");
            $table->tinyInteger('is_demo')->default(0)->comment("0=>original; 1=>Demo");
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
        Schema::dropIfExists('products');
    }
}
