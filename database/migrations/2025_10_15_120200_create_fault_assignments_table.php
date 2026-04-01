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
        Schema::create('fault_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('fault_id');
            $table->unsignedInteger('user_id');
            $table->timestamp('assigned_at');
            $table->timestamp('resolved_at')->nullable();
            $table->unsignedInteger('assigned_by')->nullable();
            $table->boolean('is_standby')->default(false);
            $table->string('region')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();

            $table->index(['fault_id']);
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fault_assignments');
    }
};