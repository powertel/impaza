<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Make faults.address nullable using driver-specific SQL
        $connection = Config::get('database.default');
        $driver = Config::get("database.connections.$connection.driver");

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE faults MODIFY address VARCHAR(255) NULL');
        } elseif ($driver === 'sqlsrv') {
            DB::statement('ALTER TABLE faults ALTER COLUMN address VARCHAR(255) NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE faults ALTER COLUMN address TYPE VARCHAR(255)');
            DB::statement('ALTER TABLE faults ALTER COLUMN address DROP NOT NULL');
        } else {
            // Fallback: attempt a generic ALTER
            DB::statement('ALTER TABLE faults ALTER COLUMN address VARCHAR(255) NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $connection = Config::get('database.default');
        $driver = Config::get("database.connections.$connection.driver");

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE faults MODIFY address VARCHAR(255) NOT NULL');
        } elseif ($driver === 'sqlsrv') {
            DB::statement('ALTER TABLE faults ALTER COLUMN address VARCHAR(255) NOT NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE faults ALTER COLUMN address SET NOT NULL');
        } else {
            DB::statement('ALTER TABLE faults ALTER COLUMN address VARCHAR(255) NOT NULL');
        }
    }
};