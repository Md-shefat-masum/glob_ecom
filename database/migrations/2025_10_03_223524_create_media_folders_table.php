<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateMediaFoldersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media_folders', function (Blueprint $table) {
            $table->id();

            $table->string('name', 150)->nullable();
            $table->string('saved_name_into_storage', 150)->nullable();
            $table->bigInteger('parent_id')->unsigned()->default(0)->nullable();
            $table->tinyInteger('is_default')->default(0)->nullable();

            $table->bigInteger('creator')->unsigned()->nullable();
            $table->string('slug', 50)->nullable();
            $table->tinyInteger('status')->unsigned()->default(1);

            $table->timestamps();
        });

        DB::table('media_folders')->insert([
            [
                'name' => 'uploads',
                'saved_name_into_storage' => 'uploads',
                'parent_id' => 0,
                'is_default' => 1,
                'creator' => 1,
                'slug' => 'upload',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('media_folders');
    }
}
