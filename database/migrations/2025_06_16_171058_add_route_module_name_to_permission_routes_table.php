<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRouteModuleNameToPermissionRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('permission_routes', function (Blueprint $table) {
            $table->string('route_module_name')->nullable()->after('route_group_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('permission_routes', function (Blueprint $table) {
            $table->dropColumn('route_module_name');
        });
    }
}
