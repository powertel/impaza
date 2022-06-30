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
        Schema::create('faults', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('customer_id');
            $table->string('contactName');
            $table->integer('phoneNumber');
            $table->string('contactEmail');
            $table->string('address');
            $table->string('accountManager');
            $table->unsignedInteger('city_id');
            $table->unsignedInteger('suburb_id');
            $table->unsignedInteger('pop_id');
            $table->unsignedInteger('link_id');
            $table->string('suspectedRfo');
            $table->string('serviceType');
            $table->string('serviceAttribute');
            $table->string('faultType')->nullable();
            $table->string('priorityLevel')->nullable();
            $table->string('assigned_department')->nullable();
            $table->string('created_by')->nullable();
            $table->integer('status_id')->nullable();
            $table->string('remarks');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('faults');
    }
};
