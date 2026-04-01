<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
            // Using raw SQL because doctrine/dbal might not be installed
            DB::statement("ALTER TABLE faults MODIFY confirmedRfo_id INT UNSIGNED NULL DEFAULT NULL");
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
            // Ensure no nulls before reverting
            DB::table('faults')->whereNull('confirmedRfo_id')->update(['confirmedRfo_id' => 1]);
            DB::statement("ALTER TABLE faults MODIFY confirmedRfo_id INT UNSIGNED NOT NULL DEFAULT 1");
        });
    }
};
