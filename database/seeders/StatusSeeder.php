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
                'description' => 'Waiting for assessment'
            ],
            [
                'status_code' => 'ASD', 
                'description' => 'Fault has been assessed',
            ],
            [
                'status_code' => 'RTN', 
                'description' => 'Fault is under rectification'
            ],
            [
                'status_code' => 'CLT', 
                'description' => 'Fault has been cleared by Technician'
            ],
            [
                'status_code' => 'CLC', 
                'description' => 'Fault has been cleared by CT'
            ],
            [
                'status_code' => 'CLN', 
                'description' => 'Fault has been cleared by NOC'
            ],
            [
                'status_code' => 'REF', 
                'description' => 'Fault has been refered'
            ],
            [
                'status_code' => 'PRK ', 
                'description' => 'Fault has been parked'
            ],
            [
                'status_code' => 'RVK ', 
                'description' => 'Fault has been revoked'
            ],
            [
                'status_code' => 'ESC', 
                'description' => 'Fault has been under escalated'
            ]
        ];
        foreach($statuses as $status){
            Status::create($status);
        }
       
    }
}
