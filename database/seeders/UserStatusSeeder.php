<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserStatus;

class UserStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user_statuses =  [
            'Assignable',
            'Unassignable',
            'Standby',
            'On Leave',
            'Away', 
        ];

        foreach ($user_statuses as $user_status) {
            UserStatus::firstOrCreate(['status_name' => $user_status]);
        }

    }
}
