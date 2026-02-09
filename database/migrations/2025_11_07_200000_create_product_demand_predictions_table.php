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
        Schema::create('product_demand_predictions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->nullable()->index();
            $table->date('predicted_for')->nullable()->index();
            $table->decimal('predicted_demand', 12, 3)->nullable();
            $table->decimal('predicted_growth_pct', 8, 2)->nullable();
            $table->enum('trend_direction', ['up', 'down', 'flat'])->default('flat');
            $table->decimal('confidence', 5, 2)->nullable();
            $table->boolean('restock_recommended')->default(false)->index();
            $table->string('model_version', 64)->nullable();
            $table->string('recommendation_reason')->nullable();
            $table->json('feature_importance')->nullable();
            $table->json('raw_payload')->nullable();
            $table->timestamp('predicted_at')->nullable();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_demand_predictions');
    }
};

