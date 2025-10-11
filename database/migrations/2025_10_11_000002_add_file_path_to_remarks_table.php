<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('remarks', 'file_path')) {
            Schema::table('remarks', function (Blueprint $table) {
                $table->string('file_path')->nullable()->after('remarkActivity_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('remarks', 'file_path')) {
            Schema::table('remarks', function (Blueprint $table) {
                $table->dropColumn('file_path');
            });
        }
    }
};