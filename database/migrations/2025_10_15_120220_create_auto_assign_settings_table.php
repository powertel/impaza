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
        Schema::create('auto_assign_settings', function (Blueprint $table) {
            $table->id();
            // Standby window for weekdays
            $table->time('standby_start_time')->default('04:30:00');
            $table->time('standby_end_time')->default('08:00:00');
            // Weekend standby toggle
            $table->boolean('weekend_standby_enabled')->default(true);
            // Whether to exclude technicians on leave from auto-assign
            $table->boolean('consider_leave')->default(true);
            // Whether to restrict auto-assign by technician region
            $table->boolean('consider_region')->default(true);
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auto_assign_settings');
    }
};