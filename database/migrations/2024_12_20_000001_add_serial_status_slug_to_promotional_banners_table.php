<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSerialStatusSlugToPromotionalBannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('promotional_banners', function (Blueprint $table) {
            $table->integer('serial')->default(1)->after('id');
            $table->tinyInteger('status')->default(1)->after('serial');
            $table->string('slug')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('promotional_banners', function (Blueprint $table) {
            $table->dropColumn(['serial', 'status', 'slug']);
        });
    }
}

