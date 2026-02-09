<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManualProductReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manual_product_returns', function (Blueprint $table) {
            $table->id();
            
            $table->string('return_code', 100)->unique()->comment('Unique return code: MR{YYMM}{0001}');
            $table->unsignedBigInteger('customer_id');
            $table->date('return_date');
            $table->text('return_reason')->nullable();
            
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            
            $table->enum('refund_method', ['wallet'])->default('wallet')->comment('Manual returns always go to wallet');
            $table->enum('refund_status', ['pending', 'completed'])->default('completed');
            
            $table->text('note')->nullable();
            $table->enum('return_status', ['approved', 'pending', 'rejected'])->default('approved');
            
            $table->unsignedBigInteger('creator')->nullable();
            $table->string('slug')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            // Indexes
            $table->index('customer_id');
            $table->index('return_code');
            $table->index('return_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('manual_product_returns');
    }
}

