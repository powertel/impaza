<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\UserStatus;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $user = User::create([
            'name' => 'Powertel',
            'email' => 'admin@powertel.co.zw',
            'password' => bcrypt('123456')
        ]);
        $position = Position::create([
            'position'=>'Senior Engineer',
            'position_id'=>'1',
        ]);
        $userstatus = UserStatus::create([
            'user_statuses' => 'Unassignable',
            'user_statuses_id'=>'1',
        ]);
        $role = Role::create(['name' => 'Admin']);

        $permissions = Permission::pluck('id','id')->all();
        $role->syncPermissions($permissions);
        $user->assignRole([$role->id]);

    }
}
