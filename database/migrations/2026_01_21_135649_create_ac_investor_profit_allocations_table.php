<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAcInvestorProfitAllocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ac_investor_profit_allocations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('investor_id');
            $table->date('start_period');
            $table->date('end_period');
            $table->decimal('profit_amount', 20, 4)->default(0);
            $table->dateTime('allocated_at')->nullable();
            $table->unsignedBigInteger('creator')->nullable();
            $table->string('slug')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
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
        Schema::dropIfExists('ac_investor_profit_allocations');
    }
}
