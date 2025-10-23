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
        Schema::table('faults', function (Blueprint $table) {
            $table->dropColumn('phoneNumber');
        });
        Schema::table('faults', function (Blueprint $table) {
            $table->string('phoneNumber', 64)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('faults', function (Blueprint $table) {
            $table->dropColumn('phoneNumber');
        });
        Schema::table('faults', function (Blueprint $table) {
            $table->integer('phoneNumber')->nullable();
        });
    }
};
