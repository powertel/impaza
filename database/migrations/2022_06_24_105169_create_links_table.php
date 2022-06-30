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
        Schema::create('links', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('suburb_id');
            $table->unsignedInteger('customer_id');
            $table->unsignedInteger('city_id');
            $table->unsignedInteger('pop_id');
            $table->string('link');
            $table->timestamps();
            $table->foreign('suburb_id')
                    ->references('id')
                    ->on('suburbs');
            $table->foreign('customer_id')
                    ->references('id')
                    ->on('customers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('links');
    }
};
