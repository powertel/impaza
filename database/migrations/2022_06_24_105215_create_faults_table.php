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
			$table->string("fault_ref_number");
            $table->unsignedInteger('customer_id');
            $table->unsignedInteger('section_id');
            $table->string('contactName');
            $table->integer('phoneNumber');
            $table->string('contactEmail');
            $table->string('address');
            $table->unsignedInteger('accountManager_id');
            $table->unsignedInteger('city_id');
            $table->unsignedInteger('suburb_id');
            $table->unsignedInteger('pop_id');
            $table->unsignedInteger('link_id');
            $table->string('suspectedRfo');
            $table->string('confirmedRfo');
            $table->string('serviceType');
            $table->string('serviceAttribute');
            $table->unsignedInteger('user_id')->nullable();
            $table->string('faultType')->nullable();
            $table->unsignedInteger('assignedTo')->nullable();
            $table->string('priorityLevel')->nullable();
            $table->unsignedInteger('status_id')->nullable();
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
