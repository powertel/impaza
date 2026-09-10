<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('material_request_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('material_request_id');
            $table->unsignedBigInteger('material_id')->nullable();
            $table->string('material_name');
            $table->string('unit')->default('pcs');
            $table->decimal('quantity_requested', 15, 2);
            $table->decimal('quantity_issued', 15, 2)->default(0);
            $table->boolean('is_available')->nullable();
            $table->text('remark')->nullable();
            $table->timestamps();

            $table->foreign('material_request_id')->references('id')->on('material_requests')->cascadeOnDelete();
            $table->foreign('material_id')->references('id')->on('materials')->nullOnDelete();

            $table->index(['material_request_id']);
            $table->index(['material_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('material_request_items');
    }
};
