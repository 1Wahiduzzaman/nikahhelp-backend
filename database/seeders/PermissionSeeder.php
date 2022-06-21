<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Role permissions
        Permission::create(['name' => 'Access Role', 'slug' => 'access-role', 'for' => 'role']);
        Permission::create(['name' => 'Create Role', 'slug' => 'create-role', 'for' => 'role']);
        Permission::create(['name' => 'Update Role', 'slug' => 'update-role', 'for' => 'role']);
        Permission::create(['name' => 'Show Role', 'slug' => 'show-role', 'for' => 'role']);
        Permission::create(['name' => 'Delete Role', 'slug' => 'delete-role', 'for' => 'role']);

        //Admin permissions
        Permission::create(['name' => 'Access Admin', 'slug' => 'access-admin', 'for' => 'admin']);
        Permission::create(['name' => 'Create Admin', 'slug' => 'create-admin', 'for' => 'admin']);
        Permission::create(['name' => 'Update Admin', 'slug' => 'update-admin', 'for' => 'admin']);
        Permission::create(['name' => 'Show Admin', 'slug' => 'show-admin', 'for' => 'admin']);
        Permission::create(['name' => 'Delete Admin', 'slug' => 'delete-admin', 'for' => 'admin']);
        Permission::create(['name' => 'Status Change Admin', 'slug' => 'status-change-admin', 'for' => 'admin']);
    }
}
