<?php

namespace Database\Seeders;

use App\Enums\UserStatus;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DefaultPermissions extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //create permissions
        $create_users = Permission::firstOrCreate(['name'=>'create-users'],
                        ['display_name' => 'Create Users', 'description' => 'Ability to Add users to system']);
        $view_users = Permission::firstOrCreate(['name'=>'view-users'],
                        ['display_name' => 'View Users', 'description' => 'Ability to view users in system']);
        $update_users = Permission::firstOrCreate(['name'=>'update-users'],
                        ['display_name' => 'Update Users', 'description' => 'Ability to update system users']);
        $delete_users = Permission::firstOrCreate(['name'=>'delete-users'],
                        ['display_name' => 'Delete Users', 'description' => 'Ability to delete users from system']);
        $approve_users = Permission::firstOrCreate(['name'=>'approve-users'],
                        ['display_name' => 'Approve Users', 'description' => 'Ability to approve new users and block users']);

        $create_categories = Permission::firstOrCreate(['name'=>'create-organisation-categories'],
                        ['display_name' => 'Create Organisation Categories', 'description' => 'Ability to Add categories to system']);
        $view_categories = Permission::firstOrCreate(['name'=>'view-organisation-categories'],
                        ['display_name' => 'View Organisation Categories', 'description' => 'Ability to view categories in system']);
        $update_categories = Permission::firstOrCreate(['name'=>'update-organisation-categories'],
                        ['display_name' => 'Update Organisation Categories', 'description' => 'Ability to update organisation categories']);
        $delete_categories = Permission::firstOrCreate(['name'=>'delete-organisation-categories'],
                        ['display_name' => 'Delete Organisation Categories', 'description' => 'Ability to delete categories from system']);


        $create_organisations = Permission::firstOrCreate(['name'=>'create-organisations'],
                        ['display_name' => 'Create Organisations', 'description' => 'Ability to Add organisations to system']);
        $view_organisations = Permission::firstOrCreate(['name'=>'view-organisations'],
                        ['display_name' => 'View Organisations', 'description' => 'Ability to view organisations in system']);
        $update_organisations = Permission::firstOrCreate(['name'=>'update-organisations'],
                        ['display_name' => 'Update Organisations', 'description' => 'Ability to update organisations']);
        $delete_organisations = Permission::firstOrCreate(['name'=>'delete-organisations'],
                        ['display_name' => 'Delete Organisations', 'description' => 'Ability to delete organisations from system']);

        $create_projects = Permission::firstOrCreate(['name'=>'create-projects'],
                        ['display_name' => 'Create Projects', 'description' => 'Ability to Add projects to system']);
        $view_projects = Permission::firstOrCreate(['name'=>'view-projects'],
                        ['display_name' => 'View Projects', 'description' => 'Ability to view projects in system']);
        $update_projects = Permission::firstOrCreate(['name'=>'update-projects'],
                        ['display_name' => 'Update Projects', 'description' => 'Ability to update projects']);
        $delete_projects = Permission::firstOrCreate(['name'=>'delete-projects'],
                        ['display_name' => 'Delete Projects', 'description' => 'Ability to delete projects from system']);
        //create admin role 
        $super_administrator = Role::firstOrCreate(['name'=>'Super Administrator','display_name'=>'Super Administrator']);
        $super_administrator->syncPermissions(Permission::where('name', 'NOT LIKE', '%blahblah%')->get());

        $subscriber = Role::firstOrCreate(['name'=>'Subscriber','display_name'=>'Subscriber']);
        $contributor = Role::firstOrCreate(['name'=>'Contributor','display_name'=>'Contributor']);
        $manager = Role::firstOrCreate(['name'=>'Manager','display_name'=>'Manager']);
        $manager->syncPermissions([$view_users, $approve_users, $create_projects, $view_projects, $update_projects, $delete_projects]);
        $contributor->syncPermissions([$view_users, $approve_users, $create_projects, $view_projects, $update_projects]);

        //assign first user role of super admin 
        
        $admin_user = User::firstOrCreate(['email'=>'admin@admin.com'],['name'=>'Super Administrator','password'=>Hash::make('12345678'), 'status' => UserStatus::Approved]);
        $admin_user->attachRole($super_administrator);
        
        
    }
}
