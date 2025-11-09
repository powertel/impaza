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
            if (!Schema::hasColumn('auto_assign_settings', 'scope_section_id')) {
                $table->unsignedInteger('scope_section_id')->nullable()->after('consider_region');
            }
            if (!Schema::hasColumn('auto_assign_settings', 'scope_region')) {
                $table->string('scope_region')->nullable()->after('scope_section_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auto_assign_settings', function (Blueprint $table) {
            if (Schema::hasColumn('auto_assign_settings', 'scope_section_id')) {
                $table->dropColumn('scope_section_id');
            }
            if (Schema::hasColumn('auto_assign_settings', 'scope_region')) {
                $table->dropColumn('scope_region');
            }
        });
    }
};