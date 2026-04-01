<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AutoAssignSetting;

class AutoAssignSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!AutoAssignSetting::query()->exists()) {
            AutoAssignSetting::create([
                'standby_start_time' => '16:30:00',
                'standby_end_time' => '06:00:00',
                'weekend_standby_enabled' => true,
                'consider_leave' => true,
                'consider_region' => true,
                'auto_assign_enabled' => false,
            ]);
        }
    }
}