<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $exists = DB::table('statuses')->where('status_code', '=', 'POI')->exists();
        if (!$exists) {
            DB::table('statuses')->insert([
                'status_code' => 'POI',
                'description' => 'Impacted by POP outage',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('statuses')->where('status_code', '=', 'POI')->delete();
    }
};

