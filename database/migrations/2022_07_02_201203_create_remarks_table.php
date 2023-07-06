<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('remarks', function (Blueprint $table) {
            $table->id();
            //$table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('fault_id');
            $table->unsignedInteger('remarkActivity_id');      
            $table->text('remark');
            $table->timestamps();
            $table->string('file_path');
            $table->foreign('fault_id')
                    ->references('id')
                    ->on('faults');
            $table->foreign('user_id')
            ->references('id')
            ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('remarks');
    }
};
