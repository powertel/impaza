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
                'status_code' => 'PRK', 
                'description' => 'Fault has been parked'
            ],
            [
                'status_code' => 'RVK', 
                'description' => 'Fault has been revoked'
            ],
            [
                'status_code' => 'ESC', 
                'description' => 'Fault  escalated to Chief Technician'
            ],
            [
                'status_code' => 'MES',
                'description' => 'Fault escalated to Manager'
            ]
        ];

        $legacyCodes = [
            'PRK ' => 'PRK',
            'RVK ' => 'RVK',
        ];
        foreach ($legacyCodes as $from => $to) {
            $legacy = Status::where('status_code', '=', $from)->first();
            if ($legacy && !Status::where('status_code', '=', $to)->exists()) {
                $legacy->update(['status_code' => $to]);
            }
        }

        foreach ($statuses as $status) {
            $code = trim((string)($status['status_code'] ?? ''));
            $description = (string)($status['description'] ?? '');
            if ($code === '' || $description === '') {
                continue;
            }

            Status::updateOrCreate(
                ['status_code' => $code],
                ['description' => $description]
            );
        }
       
    }
}
