<?php

namespace Database\Seeders\Auth;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

/**
 * Class PermissionRoleTableSeeder.
 */
class PermissionRoleTableSeeder extends Seeder
{
    /**
     * Run the database seed.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        $admin = Role::firstOrCreate(['name' => 'admin', 'title' => 'Admin', 'is_fixed' => true]);
        $manager = Role::firstOrCreate(['name' => 'manager', 'title' => 'manager', 'is_fixed' => true]);
        $employee = Role::firstOrCreate(['name' => 'employee', 'title' => 'employee', 'is_fixed' => true]);
        $user = Role::firstOrCreate(['name' => 'user', 'title' => 'user', 'is_fixed' => true]);

        $modules = config('constant.MODULES');

        foreach ($modules as $key => $module) {
            $permissions = ['view', 'add', 'edit', 'delete'];
            $module_name = strtolower(str_replace(' ', '_', $module['module_name']));
            foreach ($permissions as $key => $value) {
                $permission_name = $value.'_'.$module_name;
                Permission::firstOrCreate(['name' => $permission_name, 'is_fixed' => true]);
            }
            if (isset($module['more_permission']) && is_array($module['more_permission'])) {
                foreach ($module['more_permission'] as $key => $value) {
                    $permission_name = $module_name.'_'.$value;
                    Permission::firstOrCreate(['name' => $permission_name, 'is_fixed' => true]);
                }
            }
        }
        // Assign Permissions to Roles
        $admin->givePermissionTo(Permission::get());
        $manager->givePermissionTo([
            'view_booking',
            'add_booking',
            'edit_booking',
            'delete_booking',
            'menu_builder_header',
            'view_service',
            'add_service',
            'edit_service',
            'delete_service',
            'service_gallery',
            'view_staff',
            'add_staff',
            'edit_staff',
            'delete_staff',
            'view_customer',
            'add_customer',
            'edit_customer',
            'delete_customer',
            // 'reports_daily_booking',
            // 'reports_overall_booking',
            // 'reports_staff_payout',
            // 'reports_staff_service',
            'setting_commission',
            'view_commission',
            'add_commission',
            'edit_commission',
            'delete_commission',
        ]);

        Schema::enableForeignKeyConstraints();
    }
}
