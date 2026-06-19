<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE `links` MODIFY `city_id` INT UNSIGNED NULL');
        DB::statement('ALTER TABLE `links` MODIFY `suburb_id` INT UNSIGNED NULL');
        DB::statement('ALTER TABLE `links` MODIFY `pop_id` INT UNSIGNED NULL');
    }

    public function down(): void
    {
        DB::statement('UPDATE `links` SET `city_id` = 1 WHERE `city_id` IS NULL');
        DB::statement('UPDATE `links` SET `suburb_id` = 1 WHERE `suburb_id` IS NULL');
        DB::statement('UPDATE `links` SET `pop_id` = 1 WHERE `pop_id` IS NULL');

        DB::statement('ALTER TABLE `links` MODIFY `city_id` INT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE `links` MODIFY `suburb_id` INT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE `links` MODIFY `pop_id` INT UNSIGNED NOT NULL');
    }
};
