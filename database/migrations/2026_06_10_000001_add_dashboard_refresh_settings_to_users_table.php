<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('dashboard_auto_refresh_enabled')->default(false)->after('is_access');
            $table->unsignedInteger('dashboard_refresh_seconds')->default(60)->after('dashboard_auto_refresh_enabled');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['dashboard_auto_refresh_enabled', 'dashboard_refresh_seconds']);
        });
    }
};
