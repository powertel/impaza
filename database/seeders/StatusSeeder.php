<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Status;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {



        $statuses = [
            [
                'status_code' => 'WAS', 
                'description' => 'waiting for assessment'
            ],
            [
                'status_code' => 'ASD', 
                'description' => 'fault has been assessed',
            ],
            [
                'status_code' => 'RTN', 
                'description' => 'fault is under rectification'
            ],
            [
                'status_code' => 'RTD', 
                'description' => 'fault has been rectified'
            ],
            [
                'status_code' => 'CLT', 
                'description' => 'fault has been cleared by tech'
            ],
            [
                'status_code' => 'CLC', 
                'description' => 'fault has been cleared by CT'
            ],
            [
                'status_code' => 'CLN', 
                'description' => 'fault has been cleard by NOC'
            ],
            [
                'status_code' => 'REF', 
                'description' => 'fault has been refered'
            ],
            [
                'status_code' => 'PRK ', 
                'description' => 'fault has been parked'
            ],
            [
                'status_code' => 'RVK ', 
                'description' => 'fault has been revoked'
            ],
            [
                'status_code' => 'ESC', 
                'description' => 'fault has been under escalated'
            ]
        ];
        foreach($statuses as $status){
            Status::create($status);
        }
       
    }
}
