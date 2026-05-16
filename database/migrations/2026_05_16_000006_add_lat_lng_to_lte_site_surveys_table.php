<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('lte_site_surveys', function (Blueprint $table) {
            if (!Schema::hasColumn('lte_site_surveys', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('coordinates');
            }
            if (!Schema::hasColumn('lte_site_surveys', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }
        });
    }

    public function down()
    {
        Schema::table('lte_site_surveys', function (Blueprint $table) {
            if (Schema::hasColumn('lte_site_surveys', 'longitude')) {
                $table->dropColumn('longitude');
            }
            if (Schema::hasColumn('lte_site_surveys', 'latitude')) {
                $table->dropColumn('latitude');
            }
        });
    }
};

