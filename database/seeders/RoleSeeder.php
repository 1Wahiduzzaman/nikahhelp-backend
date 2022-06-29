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
        Role::create(['name' => 'Superadmin', 'slug' => 'superadmin', 'description' => 'Superadmin description']);
        Role::create(['name' => 'admin', 'slug' => 'admin', 'description' => 'admin provider']);
        Role::create(['name' => 'support', 'slug' => 'Support', 'description' => 'support provider']);
        Role::create(['name' => 'user', 'slug' => 'user', 'description' => 'user provider']);

        // Others Role
//        Role::create(['name' => 'Manager', 'slug' => 'manager', 'description' => 'Manager description']);
//        Role::create(['name' => 'Accountant', 'slug' => 'accountant', 'description' => 'Accountant description']);


        /* Assign role permissions to admin */
        $permissions = Permission::all();

        $roleSuperAdmin = Role::where('slug', 'superadmin')->first();
        $roleAdmin = Role::where('slug', 'admin')->first();
        $roleSupport = Role::where('slug', 'support')->first();
        $permissions->each(function ($permission) use ($roleSuperAdmin,$roleAdmin, $roleSupport) {

            $roleSuperAdmin->givePermissionTo($permission);

            if(in_array($permission->for,['admin','dashboard','team','user'])){
                $roleAdmin->givePermissionTo($permission);
            }
            if($permission->for == 'team'){
                $roleSupport->givePermissionTo($permission);
            }

        });
    }
}
