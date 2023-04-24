<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use App\Models\Section;
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

        $department = Department::create([
            'department' => 'TECHNICAL',
        ]) ;
        $section = Section::create([
            'section' => 'NOC',
             'department_id' => $department->id,

        ]);
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
