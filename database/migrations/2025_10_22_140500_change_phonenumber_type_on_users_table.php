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
            DB::statement('ALTER TABLE users MODIFY phonenumber VARCHAR(32) NULL');
        } elseif ($driver === 'sqlsrv') {
            DB::statement('ALTER TABLE users ALTER COLUMN phonenumber VARCHAR(32) NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE users ALTER COLUMN phonenumber TYPE VARCHAR(32)');
            DB::statement('ALTER TABLE users ALTER COLUMN phonenumber DROP NOT NULL');
        } else {
            DB::statement('ALTER TABLE users ALTER COLUMN phonenumber VARCHAR(32) NULL');
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
            DB::statement('ALTER TABLE users MODIFY phonenumber INT NULL');
        } elseif ($driver === 'sqlsrv') {
            DB::statement('ALTER TABLE users ALTER COLUMN phonenumber INT NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE users ALTER COLUMN phonenumber TYPE INT USING phonenumber::integer');
            DB::statement('ALTER TABLE users ALTER COLUMN phonenumber DROP NOT NULL');
        } else {
            DB::statement('ALTER TABLE users ALTER COLUMN phonenumber INT NULL');
        }
    }
};