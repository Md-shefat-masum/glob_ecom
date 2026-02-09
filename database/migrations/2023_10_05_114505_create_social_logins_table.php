<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSocialLoginsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('social_logins', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('fb_login_status')->default(0)->comment("0=>Inactive; 1=>Active");
            $table->string('fb_app_id')->nullable();
            $table->string('fb_app_secret')->nullable();
            $table->string('fb_redirect_url')->nullable();
            $table->tinyInteger('gmail_login_status')->default(0)->comment("0=>Inactive; 1=>Active");
            $table->string('gmail_client_id')->nullable();
            $table->string('gmail_secret_id')->nullable();
            $table->string('gmail_redirect_url')->nullable();
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
        Schema::dropIfExists('social_logins');
    }
}
