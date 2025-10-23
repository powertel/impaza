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
            $table->string('phoneNumber', 64)->nullable()->after('customer_id')->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('phonenumber', 64)->nullable()->after('email')->change();
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
            $table->string('phoneNumber', 64)->nullable()->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('phonenumber', 64)->nullable()->change();
        });
    }
};