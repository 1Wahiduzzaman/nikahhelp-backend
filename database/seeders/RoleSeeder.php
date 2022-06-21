<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         // Admin Role
        Role::create(['name' => 'Super Admin', 'slug' => 'super-admin', 'description' => 'Super Admin description']);
        Role::create(['name' => 'Support', 'slug' => 'support', 'description' => 'Support provider']);

        // Others Role
//        Role::create(['name' => 'Manager', 'slug' => 'manager', 'description' => 'Manager description']);
//        Role::create(['name' => 'Accountant', 'slug' => 'accountant', 'description' => 'Accountant description']);


        /* Assign role permissions to admin */
        $permissions = Permission::all();

        $roleSuperAdmin = Role::where('slug', 'super-admin')->first();
        $roleSupport = Role::where('slug', 'support')->first();
        $permissions->each(function ($permission) use ($roleSuperAdmin, $roleSupport) {

            $roleSuperAdmin->givePermissionTo($permission);

            if($permission->for != 'admin'){
                $roleSupport->givePermissionTo($permission);
            }

        });
    }
}
