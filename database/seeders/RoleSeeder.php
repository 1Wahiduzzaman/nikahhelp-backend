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
        Role::create(['name' => 'Admin', 'slug' => 'admin', 'description' => 'Admin description']);
        Role::create(['name' => 'Support', 'slug' => 'support', 'description' => 'Support provider']);

        // Others Role
//        Role::create(['name' => 'Manager', 'slug' => 'manager', 'description' => 'Manager description']);
//        Role::create(['name' => 'Accountant', 'slug' => 'accountant', 'description' => 'Accountant description']);


        /* Assign role permissions to admin */
        $permissions = Permission::all();

        $roleAdmin = Role::where('slug', 'admin')->first();
        $roleSupport = Role::where('slug', 'support')->first();
        $permissions->each(function ($permission) use ($roleAdmin, $roleSupport) {

            $roleAdmin->givePermissionTo($permission);

            if($permission->for == 'team'){
                $roleSupport->givePermissionTo($permission);
            }

        });
    }
}
