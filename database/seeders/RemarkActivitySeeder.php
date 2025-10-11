<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RemarkActivity;

class RemarkActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $activities = [
            'ON LOGGING',
            'ON RECTIFICATION',
            'ON REFER',
            'ON ASSESSMENT',
            'ON TECHNICIAN CLEAR',
            'ON CHIEF-TECH CLEAR',
            'ON CHIEF-TECH REASSIGN',
            'ON NOC CLEAR',
            'ON REASSIGN APPROVE',
            'ON FAULT EDIT',
         ];
       
         foreach ($activities as $activity) {
            RemarkActivity::create(['activity' => $activity]);
         }
    }
}
