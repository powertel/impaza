<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_usage_report_deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('trigger_type', 32)->default('scheduled');
            $table->string('status', 16)->default('pending');
            $table->string('subject')->nullable();
            $table->string('primary_recipient')->nullable();
            $table->text('recipients')->nullable();
            $table->timestamp('period_start')->nullable();
            $table->timestamp('period_end')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->text('error_message')->nullable();
            $table->unsignedInteger('initiated_by')->nullable();
            $table->timestamps();

            $table->index(['status', 'started_at']);
            $table->index(['trigger_type', 'started_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_usage_report_deliveries');
    }
};
