<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Make remarks.file_path nullable (MySQL-specific)
        DB::statement('ALTER TABLE remarks MODIFY file_path VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to NOT NULL
        DB::statement('ALTER TABLE remarks MODIFY file_path VARCHAR(255) NOT NULL');
    }
};