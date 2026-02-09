<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('verification_code')->nullable();
            $table->string('password')->nullable();
            $table->string('provider_name')->nullable();
            $table->string('provider_id')->nullable();
            $table->rememberToken();
            $table->tinyInteger('user_type')->comment("1=>Admin; 2=>User/Shop; 3=>Customer")->default(3);
            $table->longText('address')->nullable();
            $table->double('balance')->comment("In BDT")->default(0);

            $table->tinyInteger('delete_request_submitted')->comment("0=>No; 1=>Yes")->default(0);
            $table->dateTime('delete_request_submitted_at')->nullable();

            $table->tinyInteger('status')->comment("1=>Active; 0=>Inactive")->default(1);
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
        Schema::dropIfExists('users');
    }
}
