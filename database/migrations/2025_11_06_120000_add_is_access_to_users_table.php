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
        Schema::table('users', function (Blueprint $table) {
            // Add an access flag after user_status; 0 = enabled, 1 = disabled
            if (!Schema::hasColumn('users', 'is_access')) {
                $table->tinyInteger('is_access')->default(0)->after('user_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_access')) {
                $table->dropColumn('is_access');
            }
        });
    }
};