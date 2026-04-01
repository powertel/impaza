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
        Schema::table('suburbs', function (Blueprint $table) {
            $table->dropForeign(['zone_id']); // Assuming foreign key exists
            $table->dropColumn('zone_id');
        });

        Schema::table('pops', function (Blueprint $table) {
            $table->foreignId('zone_id')->nullable()->constrained('zones')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pops', function (Blueprint $table) {
            $table->dropForeign(['zone_id']);
            $table->dropColumn('zone_id');
        });

        Schema::table('suburbs', function (Blueprint $table) {
            $table->foreignId('zone_id')->nullable()->constrained('zones')->nullOnDelete();
        });
    }
};
