<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailConfiguresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_configures', function (Blueprint $table) {
            $table->id();
            $table->string('host');
            $table->integer('port');
            $table->string('email');
            $table->string('password');
            $table->string('mail_from_name')->nullable();
            $table->string('mail_from_email')->nullable();
            $table->tinyInteger('encryption')->default(0)->comment("0=>None; 1=>TLS; 2=>SSL");
            $table->string('slug');
            $table->tinyInteger('status')->default(1)->comment("0=>Inactive; 1=>Active");
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
        Schema::dropIfExists('email_configures');
    }
}
