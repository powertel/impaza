<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fault_referrals', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('fault_id');
            $table->unsignedInteger('from_section_id')->nullable();
            $table->unsignedInteger('to_section_id');
            $table->unsignedInteger('referred_by');
            $table->unsignedInteger('previous_status_id')->nullable();
            $table->text('work_note')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();

            $table->index(['fault_id']);
            $table->index(['to_section_id']);
            $table->index(['completed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fault_referrals');
    }
};