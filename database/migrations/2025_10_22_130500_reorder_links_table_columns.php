<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Reorder columns in links table to the requested format.
     */
    public function up(): void
    {
        // id stays first
        // Move customer_id next
        DB::statement("ALTER TABLE links MODIFY COLUMN customer_id INT UNSIGNED NOT NULL AFTER id");
        // contract_number after customer_id
        DB::statement("ALTER TABLE links MODIFY COLUMN contract_number INT UNSIGNED NULL AFTER customer_id");
        // jcc_number after contract_number
        DB::statement("ALTER TABLE links MODIFY COLUMN jcc_number VARCHAR(255) NULL AFTER contract_number");
        // sapcodes after jcc_number
        DB::statement("ALTER TABLE links MODIFY COLUMN sapcodes VARCHAR(255) NULL AFTER jcc_number");
        // comment after sapcodes
        DB::statement("ALTER TABLE links MODIFY COLUMN comment TEXT NULL AFTER sapcodes");
        // quantity after comment
        DB::statement("ALTER TABLE links MODIFY COLUMN quantity INT UNSIGNED NULL AFTER comment");
        // service_type after quantity
        DB::statement("ALTER TABLE links MODIFY COLUMN service_type VARCHAR(255) NULL AFTER quantity");
        // capacity after service_type
        DB::statement("ALTER TABLE links MODIFY COLUMN capacity VARCHAR(255) NULL AFTER service_type");
        // city_id after capacity
        DB::statement("ALTER TABLE links MODIFY COLUMN city_id INT UNSIGNED NOT NULL AFTER capacity");
        // suburb_id after city_id
        DB::statement("ALTER TABLE links MODIFY COLUMN suburb_id INT UNSIGNED NOT NULL AFTER city_id");
        // pop_id after suburb_id
        DB::statement("ALTER TABLE links MODIFY COLUMN pop_id INT UNSIGNED NOT NULL AFTER suburb_id");
        // linkType_id after pop_id
        DB::statement("ALTER TABLE links MODIFY COLUMN linkType_id INT UNSIGNED NOT NULL AFTER pop_id");
        // link_status after linkType_id
        DB::statement("ALTER TABLE links MODIFY COLUMN link_status INT UNSIGNED NOT NULL AFTER linkType_id");
        // link after link_status
        DB::statement("ALTER TABLE links MODIFY COLUMN link VARCHAR(255) NOT NULL AFTER link_status");
        // timestamps remain at the end
    }

    /**
     * Attempt to revert to the previous approximate order.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE links MODIFY COLUMN contract_number INT UNSIGNED NULL AFTER id");
        DB::statement("ALTER TABLE links MODIFY COLUMN suburb_id INT UNSIGNED NOT NULL AFTER contract_number");
        DB::statement("ALTER TABLE links MODIFY COLUMN customer_id INT UNSIGNED NOT NULL AFTER suburb_id");
        DB::statement("ALTER TABLE links MODIFY COLUMN city_id INT UNSIGNED NOT NULL AFTER customer_id");
        DB::statement("ALTER TABLE links MODIFY COLUMN pop_id INT UNSIGNED NOT NULL AFTER city_id");
        DB::statement("ALTER TABLE links MODIFY COLUMN linkType_id INT UNSIGNED NOT NULL AFTER pop_id");
        DB::statement("ALTER TABLE links MODIFY COLUMN link_status INT UNSIGNED NOT NULL AFTER linkType_id");
        DB::statement("ALTER TABLE links MODIFY COLUMN link VARCHAR(255) NOT NULL AFTER link_status");
        DB::statement("ALTER TABLE links MODIFY COLUMN jcc_number VARCHAR(255) NULL AFTER contract_number");
        DB::statement("ALTER TABLE links MODIFY COLUMN sapcodes VARCHAR(255) NULL AFTER jcc_number");
        DB::statement("ALTER TABLE links MODIFY COLUMN comment TEXT NULL AFTER sapcodes");
        DB::statement("ALTER TABLE links MODIFY COLUMN quantity INT UNSIGNED NULL AFTER comment");
        DB::statement("ALTER TABLE links MODIFY COLUMN service_type VARCHAR(255) NULL AFTER quantity");
        DB::statement("ALTER TABLE links MODIFY COLUMN capacity VARCHAR(255) NULL AFTER service_type");
    }
};