<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('auto_assign_settings', function (Blueprint $table) {
            $table->boolean('consider_zones')->default(false)->after('consider_region');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('auto_assign_settings', function (Blueprint $table) {
            $table->dropColumn('consider_zones');
        });
    }
};
