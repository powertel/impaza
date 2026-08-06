<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_usage_report_settings', function (Blueprint $table) {
            $table->unsignedTinyInteger('send_day')
                ->default(1)
                ->comment('1=Monday .. 7=Sunday');
        });
    }

    public function down(): void
    {
        Schema::table('system_usage_report_settings', function (Blueprint $table) {
            $table->dropColumn('send_day');
        });
    }
};
