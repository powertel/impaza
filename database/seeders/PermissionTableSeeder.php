<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',
            'fault-list',
            'fault-create',
            'fault-edit',
            'fault-delete',
            'remark-create',
            'remark-view',
            'clear-fault',
            'request-material',
            'rectify-fault',
            'refer-fault',
            'link-list',
            'link-create',
            'link-edit',
            'link-delete',
            'customer-list',
            'customer-create',
            'customer-edit',
            'customer-delete',
            'account-manager-list',
            'account-manager-create',
            'account-manager-edit',
            'account-manager-delete',
            'department-list',
            'department-create',
            'department-edit',
            'department-delete',
            'city-list',
            'city-create',
            'city-edit',
            'city-delete',
            'request-permit',
            'approve-permit',
            'pop-list',
            'pop-create',
            'pop-edit',
            'pop-delete',
            'location-list',
            'location-create',
            'location-edit',
            'location-delete',
            'fault-assessment',
            'assign-fault',
            're-assign-fault',
            'permissions',
            'finance',
            'permit-list',
            'materials',
            'reports'
         ];
       
         foreach ($permissions as $permission) {
              Permission::create(['name' => $permission]);
         }
    }
}
