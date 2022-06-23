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

        //Admin permissions
        Permission::create(['name' => 'Dashboard Assess', 'slug' => 'dashboard-assess', 'for' => 'dashboard']);
        Permission::create(['name' => 'Get Active User Data', 'slug' => 'get-active-user', 'for' => 'dashboard']);
        Permission::create(['name' => 'Get Pending User Data', 'slug' => 'get-pending-user', 'for' => 'dashboard']);
        Permission::create(['name' => 'Get Approved User Data', 'slug' => 'get-approved-user', 'for' => 'dashboard']);
        Permission::create(['name' => 'Get Verified User Data', 'slug' => 'get-verified-user', 'for' => 'dashboard']);
        Permission::create(['name' => 'Get Rejected User Data', 'slug' => 'get-rejected-user', 'for' => 'dashboard']);
        Permission::create(['name' => 'Get Particular User Data', 'slug' => 'get-particular-user', 'for' => 'dashboard']);
        Permission::create(['name' => 'Get Particular Candidate Data', 'slug' => 'get-particular-candidate', 'for' => 'dashboard']);
        Permission::create(['name' => 'Get Particular Representative Data', 'slug' => 'get-particular-representative', 'for' => 'dashboard']);

        Permission::create(['name' => 'Get Team Subscription Data', 'slug' => 'get-team-subscription-data', 'for' => 'team']);
        Permission::create(['name' => 'Get Team Data', 'slug' => 'get-team-data', 'for' => 'team']);
        Permission::create(['name' => 'Get Deleted Team List', 'slug' => 'get-deleted-team-list', 'for' => 'team']);
        Permission::create(['name' => 'Get Connected Team', 'slug' => 'get-connected-team', 'for' => 'team']);
        Permission::create(['name' => 'Delete Team', 'slug' => 'delete-team', 'for' => 'team']);

        Permission::create(['name' => 'Get All User', 'slug' => 'get-all-user', 'for' => 'user']);
        Permission::create(['name' => 'Send Global Notification', 'slug' => 'send-global-notification', 'for' => 'user']);

    }
}
