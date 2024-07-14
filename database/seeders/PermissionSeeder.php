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
        Permission::create(['name' => 'Access Role', 'slug' => 'ACCESS_ROLE', 'for' => 'role']);
        Permission::create(['name' => 'Create Role', 'slug' => 'CREATE_ROLE', 'for' => 'role']);
        Permission::create(['name' => 'Update Role', 'slug' => 'UPDATE_ROLE', 'for' => 'role']);
        Permission::create(['name' => 'Show Role', 'slug' => 'SHOW_ROLE', 'for' => 'role']);
        Permission::create(['name' => 'Delete Role', 'slug' => 'DELETE_ROLE', 'for' => 'role']);

        //Admin permissions
        Permission::create(['name' => 'Access Admin', 'slug' => 'ACCESS_ADMIN', 'for' => 'super_admin']);
        Permission::create(['name' => 'Create Admin', 'slug' => 'CREATE_ADMIN', 'for' => 'super_admin']);
        Permission::create(['name' => 'Update Admin', 'slug' => 'UPDATE_ADMIN', 'for' => 'super_admin']);
        Permission::create(['name' => 'Show Admin', 'slug' => 'SHOW_ADMIN', 'for' => 'super_admin']);
        Permission::create(['name' => 'Delete Admin', 'slug' => 'DELETE_ADMIN', 'for' => 'super_admin']);
        Permission::create(['name' => 'Status Change Admin', 'slug' => 'STATUS_CHANGE_ADMIN', 'for' => 'super_admin']);

        //Admin permissions
        Permission::create(['name' => 'Dashboard Assess', 'slug' => 'DASHBOARD_ASSESS', 'for' => 'super_admin']);
        Permission::create(['name' => 'Get Active User Data', 'slug' => 'GET_ACTIVE_USER', 'for' => 'super_admin']);
        Permission::create(['name' => 'Get Pending User Data', 'slug' => 'GET_PENDING_USER', 'for' => 'super_admin']);
        Permission::create(['name' => 'Get Approved User Data', 'slug' => 'GET_APPROVED_USER', 'for' => 'super_admin']);
        Permission::create(['name' => 'Get Verified User Data', 'slug' => 'GET_VERIFIED_USER', 'for' => 'super_admin']);
        Permission::create(['name' => 'Get Rejected User Data', 'slug' => 'GET_REJECTED_USER', 'for' => 'super_admin']);
        Permission::create(['name' => 'Get Particular User Data', 'slug' => 'GET_PARTICULAR_USER', 'for' => 'super_admin']);
        Permission::create(['name' => 'Get Particular Candidate Data', 'slug' => 'GET_PARTICULAR_CANDIDATE', 'for' => 'super_admin']);
        Permission::create(['name' => 'Get Particular Representative Data', 'slug' => 'GET_PARTICULAR_REPRESENTATIVE', 'for' => 'super_admin']);

        Permission::create(['name' => 'Get Team Subscription Data', 'slug' => 'GET_TEAM_SUBSCRIPTION_DATA', 'for' => 'team']);
        Permission::create(['name' => 'Get Team Data', 'slug' => 'GET_TEAM_DATA', 'for' => 'team']);
        Permission::create(['name' => 'Get Deleted Team List', 'slug' => 'GET_DELETED_TEAM_LIST', 'for' => 'team']);
        Permission::create(['name' => 'Get Connected Team', 'slug' => 'GET_CONNECTED_TEAM', 'for' => 'team']);
        Permission::create(['name' => 'Delete Team', 'slug' => 'DELETE_TEAM', 'for' => 'team']);

        Permission::create(['name' => 'Get All User', 'slug' => 'GET_ALL_USER', 'for' => 'user']);
        Permission::create(['name' => 'Send Global Notification', 'slug' => 'SEND_GLOBAL_NOTIFICATION', 'for' => 'user']);

        Permission::create(['name' => 'Admin Can Access', 'slug' => 'CAN_ACCESS_ADMIN', 'for' => 'super_admin']);
        Permission::create(['name' => 'Can Access Candidate Registration form', 'slug' => 'CAN_ACCESS_CANDIDATE_REGISTRATION_FORM', 'for' => 'super_admin']);
        Permission::create(['name' => 'Can Access Representative Registration form', 'slug' => 'CAN_ACCESS_REPRESENTATIVE_REGISTRATION_FORM', 'for' => 'super_admin']);
        Permission::create(['name' => 'Can Access Dashboard', 'slug' => 'CAN_ACCESS_DASHBOARD', 'for' => 'super_admin']);
        Permission::create(['name' => 'Reject user', 'slug' => 'VERIFY_REJECT_USER', 'for' => 'super_admin']);
        Permission::create(['name' => 'Support Access', 'slug' => 'CAN_ACCESS_SUPPORT', 'for' => 'support']);

    }
}
