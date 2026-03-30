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
            'ON EDIT',
            'ON FAULT EDIT',
            'ON CALL CENTRE ASSESSMENT',
            'On Call Centre Clear',
            'ON RECTIFICATION',
            'ON REFER',
            'ON ASSESSMENT',
            'ON CHIEF-TECH ASSIGN',
            'ON TECHNICIAN CLEAR',
            'ON CHIEF-TECH CLEAR',
            'ON CHIEF-TECH REASSIGN',
            'ON NOC CLEAR',
            'ON REASSIGN APPROVE',
            'ON MATERIAL REQUEST',
            'ON REQUEST PERMIT',
         ];
       
        foreach (array_values(array_unique($activities)) as $activity) {
            RemarkActivity::firstOrCreate(['activity' => $activity]);
        }
    }
}
