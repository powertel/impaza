<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

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
            'my-fault-create',
            'my-fault-edit',
            'my-fault-delete',
            'department-faults-list',
            'department-faults-create',
            'department-faults-edit',
            'department-faults-delete',
            'assigned-fault-list',
            'assessment-fault-list',
            'assessment-fault-create',
            'assessment-fault-edit',
            'assessment-fault-delete',
            'noc-clear-faults-list',
            'noc-clear-faults-create',
            'chief-tech-clear-faults-list',
            'chief-tech-clear-faults-create',
            're-assign-fault',

            'remark-create',
            'remark-view',
            'clear-fault',
            'request-material',
            'rectify-fault',
            'rectify-list',
            'rectify-create',
            'rectify-edit',
            'rectify-delete',

            'refer-fault',
            'request-permit',
            'approve-permit',
            'fault-assessment',
            'permissions',
            'finance',
            'finance-link-update',
            'permit-list',
            'materials',
            'material',
            'reports',
            'resolved-faults-list',
            'referred-faults',
            'call-centre-reports',
            'performance-reports',
            'noc-clear-faults-clear',
            'noc-clear-faults-delete',
            'chief-tech-clear-faults-clear',
            'chief-tech-clear-faults-delete',
            'technician-configuration',
            'assign-fault',
            'manage-faults',
            'chief-tech-escalate',
            'chief-tech-return-to-technician',
            'manager-return-to-chief-tech',

            // Dashboard permissions
            'dashboard-open-faults',
            'dashboard-fault-age',
            'dashboard-resolution-metrics',
            'dashboard-recent-faults',
         ];

         foreach (array_values(array_unique($permissions)) as $permission) {
              Permission::firstOrCreate(['name' => $permission]);
         }

         $role = Role::find(1);
         if ($role) {
             $all = Permission::pluck('id','name')->all();
             $role->syncPermissions($all);
             $user = User::find(1);
             if ($user) {
                 $user->assignRole([$role->id]);
             }
         }
    }
}
