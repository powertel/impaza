<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('faults', function (Blueprint $table) {
            if (!Schema::hasColumn('faults', 'root_fault_id')) {
                $table->unsignedInteger('root_fault_id')->nullable()->after('id');
                $table->index('root_fault_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('faults', function (Blueprint $table) {
            if (Schema::hasColumn('faults', 'root_fault_id')) {
                $table->dropIndex(['root_fault_id']);
                $table->dropColumn('root_fault_id');
            }
        });
    }
};

