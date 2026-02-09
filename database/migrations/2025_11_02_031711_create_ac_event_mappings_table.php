<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAcEventMappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ac_event_mappings', function (Blueprint $table) {
            $table->id();
            
            $table->string('event_name', 100)->unique();
            $table->string('event_description', 255)->nullable();
            
            // Primary Debit/Credit Accounts
            $table->unsignedBigInteger('debit_account_id')->nullable();
            $table->unsignedBigInteger('credit_account_id')->nullable();
            
            // Secondary Debit/Credit Accounts (for dual-entry events like sales with COGS)
            $table->unsignedBigInteger('secondary_debit_account_id')->nullable();
            $table->unsignedBigInteger('secondary_credit_account_id')->nullable();
            
            $table->boolean('is_active')->default(true);
            $table->text('note')->nullable();
            
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('debit_account_id')->references('id')->on('ac_accounts')->onDelete('set null');
            $table->foreign('credit_account_id')->references('id')->on('ac_accounts')->onDelete('set null');
            $table->foreign('secondary_debit_account_id')->references('id')->on('ac_accounts')->onDelete('set null');
            $table->foreign('secondary_credit_account_id')->references('id')->on('ac_accounts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ac_event_mappings');
    }
}
