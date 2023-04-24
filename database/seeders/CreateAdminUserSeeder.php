<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use App\Models\Section;
use App\Models\Position;
use App\Models\UserStatus;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

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
        $position = Position::create([
            'position'=>'Senior Engineer',
            'section_id' => $section->id,
        ]);
        $user_statuses= UserStatus::create([
            'user_statuses' => 'status_name',
        ]) ;
        $user = User::create([
            'name' => 'Powertel',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('123456')
        ]);

        $role = Role::create(['name' => 'Admin']);

        $permissions = Permission::pluck('id','id')->all();

        $role->syncPermissions($permissions);

        $user->assignRole([$role->id]);

    }
}
