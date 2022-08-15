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
        Schema::create('fault_section_users', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('fault_id');
            $table->unsignedInteger('section_id');
            $table->unsignedInteger('user_id')->nullable();
            $table->timestamps();
            $table->foreign('fault_id')
                ->references('id')
                ->on('faults');
            $table->foreign('section_id')
                ->references('id')
                ->on('sections');
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
        Schema::dropIfExists('fault_section_users');
    }
};
