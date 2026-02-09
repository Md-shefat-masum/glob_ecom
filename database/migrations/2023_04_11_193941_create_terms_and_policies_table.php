<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTermsAndPoliciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('terms_and_policies', function (Blueprint $table) {
            $table->id();
            $table->longText('terms')->nullable();
            $table->longText('privacy_policy')->nullable();
            $table->longText('shipping_policy')->nullable();
            $table->longText('return_policy')->nullable();
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
        Schema::dropIfExists('terms_and_policies');
    }
}
