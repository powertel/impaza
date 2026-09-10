<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::dropIfExists('materials');

        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->nullable()->unique();
            $table->string('category')->nullable();
            $table->string('unit')->default('pcs');
            $table->decimal('quantity_on_hand', 15, 2)->default(0);
            $table->decimal('reorder_level', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->index(['category', 'is_active']);
            $table->index(['name']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('materials');
    }
};
