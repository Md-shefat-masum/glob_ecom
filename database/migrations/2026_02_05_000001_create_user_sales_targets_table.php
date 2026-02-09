<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSalesTargetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_sales_targets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->date('date');
            $table->decimal('target', 15, 2)->default(0);
            $table->decimal('completed', 15, 2)->default(0);
            $table->decimal('remains', 15, 2)->default(0);
            $table->boolean('is_evaluated')->default(false);
            $table->text('note')->nullable();

            $table->string('status')->default(1);
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_sales_targets');
    }
}
