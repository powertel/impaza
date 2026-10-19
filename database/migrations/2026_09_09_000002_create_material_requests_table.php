<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('material_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->unsignedInteger('fault_id');
            $table->unsignedInteger('requested_by');
            $table->unsignedInteger('processed_by')->nullable();
            $table->text('technician_note')->nullable();
            $table->text('stores_note')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->foreign('fault_id')->references('id')->on('faults')->cascadeOnDelete();
            $table->foreign('requested_by')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('processed_by')->references('id')->on('users')->nullOnDelete();

            $table->index(['status', 'created_at']);
            $table->index(['fault_id']);
            $table->index(['requested_by']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('material_requests');
    }
};
