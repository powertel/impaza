<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('auto_assign_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('auto_assign_settings', 'auto_assign_enabled')) {
                $table->boolean('auto_assign_enabled')->default(false)->after('consider_region');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auto_assign_settings', function (Blueprint $table) {
            if (Schema::hasColumn('auto_assign_settings', 'auto_assign_enabled')) {
                $table->dropColumn('auto_assign_enabled');
            }
        });
    }
};