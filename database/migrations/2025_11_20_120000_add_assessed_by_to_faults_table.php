<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('faults', 'assessed_by')) {
            Schema::table('faults', function (Blueprint $table) {
                $table->unsignedInteger('assessed_by')->nullable()->after('assignedTo');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('faults', 'assessed_by')) {
            Schema::table('faults', function (Blueprint $table) {
                $table->dropColumn('assessed_by');
            });
        }
    }
};