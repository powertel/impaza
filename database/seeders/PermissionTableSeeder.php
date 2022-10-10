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

            'pop-list',
            'pop-create',
            'pop-edit',
            'pop-delete',

            'location-list',
            'location-create',
            'location-edit',
            'location-delete',

            'my-fault-list',
            'department-faults-list',
            'assessement-fault-list',
            'noc-clear-faults-list',
            'chief-tech-clear-faults-list',
            'remark-create',
            'remark-view',
            'clear-fault',
            'request-material',
            'rectify-fault',
            're-assign-fault',
            'refer-fault',
            'request-permit',
            'approve-permit',
            'fault-assessment',
            'permissions',
            'finance',
            'finance-link-update',
            'permit-list',
            'materials',
            'reports',
            'noc-clear-faults-clear',
            'chief-tech-clear-faults-clear'
         ];
       
         foreach ($permissions as $permission) {
              Permission::create(['name' => $permission]);
         }
    }
}
