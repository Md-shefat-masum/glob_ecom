<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoogleRecaptchasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('google_recaptchas', function (Blueprint $table) {
            $table->id();
            $table->string('captcha_site_key')->nullable();
            $table->string('captcha_secret_key')->nullable();
            $table->tinyInteger('status')->default(0)->comment("1=>Active; 0=>Inactive");
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
        Schema::dropIfExists('google_recaptchas');
    }
}
