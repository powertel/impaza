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
        $connection = Config::get('database.default');
        $driver = Config::get("database.connections.$connection.driver");

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE faults MODIFY city_id INT UNSIGNED NULL');
            DB::statement('ALTER TABLE faults MODIFY suburb_id INT UNSIGNED NULL');
            DB::statement('ALTER TABLE faults MODIFY pop_id INT UNSIGNED NULL');
            DB::statement('ALTER TABLE faults MODIFY serviceType VARCHAR(255) NULL');
        } elseif ($driver === 'sqlsrv') {
            DB::statement('ALTER TABLE faults ALTER COLUMN city_id INT NULL');
            DB::statement('ALTER TABLE faults ALTER COLUMN suburb_id INT NULL');
            DB::statement('ALTER TABLE faults ALTER COLUMN pop_id INT NULL');
            DB::statement('ALTER TABLE faults ALTER COLUMN serviceType VARCHAR(255) NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE faults ALTER COLUMN city_id DROP NOT NULL');
            DB::statement('ALTER TABLE faults ALTER COLUMN suburb_id DROP NOT NULL');
            DB::statement('ALTER TABLE faults ALTER COLUMN pop_id DROP NOT NULL');
            DB::statement('ALTER TABLE faults ALTER COLUMN serviceType DROP NOT NULL');
        } else {
            // Fallback generic SQL
            DB::statement('ALTER TABLE faults ALTER COLUMN city_id INT NULL');
            DB::statement('ALTER TABLE faults ALTER COLUMN suburb_id INT NULL');
            DB::statement('ALTER TABLE faults ALTER COLUMN pop_id INT NULL');
            DB::statement('ALTER TABLE faults ALTER COLUMN serviceType VARCHAR(255) NULL');
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
            DB::statement('ALTER TABLE faults MODIFY city_id INT UNSIGNED NOT NULL');
            DB::statement('ALTER TABLE faults MODIFY suburb_id INT UNSIGNED NOT NULL');
            DB::statement('ALTER TABLE faults MODIFY pop_id INT UNSIGNED NOT NULL');
            DB::statement('ALTER TABLE faults MODIFY serviceType VARCHAR(255) NOT NULL');
        } elseif ($driver === 'sqlsrv') {
            DB::statement('ALTER TABLE faults ALTER COLUMN city_id INT NOT NULL');
            DB::statement('ALTER TABLE faults ALTER COLUMN suburb_id INT NOT NULL');
            DB::statement('ALTER TABLE faults ALTER COLUMN pop_id INT NOT NULL');
            DB::statement('ALTER TABLE faults ALTER COLUMN serviceType VARCHAR(255) NOT NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE faults ALTER COLUMN city_id SET NOT NULL');
            DB::statement('ALTER TABLE faults ALTER COLUMN suburb_id SET NOT NULL');
            DB::statement('ALTER TABLE faults ALTER COLUMN pop_id SET NOT NULL');
            DB::statement('ALTER TABLE faults ALTER COLUMN serviceType SET NOT NULL');
        } else {
            DB::statement('ALTER TABLE faults ALTER COLUMN city_id INT NOT NULL');
            DB::statement('ALTER TABLE faults ALTER COLUMN suburb_id INT NOT NULL');
            DB::statement('ALTER TABLE faults ALTER COLUMN pop_id INT NOT NULL');
            DB::statement('ALTER TABLE faults ALTER COLUMN serviceType VARCHAR(255) NOT NULL');
        }
    }
};