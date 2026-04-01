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
        Schema::create('fault_stage_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('fault_id');
            $table->unsignedInteger('status_id');
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->unsignedInteger('started_by')->nullable();
            $table->unsignedInteger('ended_by')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();

            $table->index(['fault_id']);
            $table->index(['status_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fault_stage_logs');
    }
};