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
            'link-create',
            'link-edit',
            'link-list',
            'customer-list',
            'customer-create',
            'customer-edit',
            'account-manager-list',
            'account-manager-create',
            'account-manager-edit',
            'department-create',
            'department-edit',
            'department-list',
            'city-create',
            'city-edit',
            'city-list',
            'request-permit',
            'approve-permit',
            'pop-list',
            'pop-create',
            'pop-edit',
            'location-list',
            'location-create',
            'location-edit',
            'fault-assessment',
            'assign-fault',
         ];
       
         foreach ($permissions as $permission) {
              Permission::create(['name' => $permission]);
         }
    }
}
