<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Reset Cached Permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Define Roles
        $rolesList = [
            'Super Admin',
            'Admin',
            'Faculty',
            'Staff',
            'Student'
        ];

        // 3. Define Permissions
        $permissionsList = [
            // User Management
            'user-list', 'user-create', 'user-edit', 'user-delete',

            // Role Management
            'role-list', 'role-create', 'role-edit', 'role-delete',

            // Permission Management
            'permission-list', 'permission-create', 'permission-edit', 'permission-delete',

            // Batch Management
            'view_batches', 'create_batches', 'edit_batches', 'delete_batches', 'enroll_students',

            // Course Management
            'view unpublished courses', 'create courses', 'edit courses', 
            'delete courses', 'restore courses', 'force delete courses',

            // Student Management
            'student-list', 'student-create', 'student-edit', 'student-delete',

            // Enrollment Management (NEW)
            'enrollment-list', 'enrollment-view', 'enrollment-create', 'enrollment-edit', 'enrollment-delete',

            // Certificate Management (NEW)
            'certificate-list', 'certificate-download', 'certificate-approve-faculty', 'certificate-approve-admin',
        ];

        // 4. Create Permissions
        foreach ($permissionsList as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // 5. Create Roles
        foreach ($rolesList as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        // 6. Assign Permissions to Roles
        
        // Admin gets ALL permissions
        $roleAdmin = Role::findByName('Admin');
        $roleAdmin->syncPermissions($permissionsList); // syncPermissions is safer than givePermissionTo for bulk updates

        // Staff gets specific access
        $roleStaff = Role::findByName('Staff');
        $roleStaff->syncPermissions([
            'user-list', 'role-list', 'permission-list', 'student-list', 
            'view_batches', 
            // Enrollment access for staff
            'enrollment-list', 'enrollment-view', 'enrollment-create', 'enrollment-edit'
        ]);
        
        // Faculty gets specific access
        $roleFaculty = Role::findByName('Faculty');
        $roleFaculty->syncPermissions([
            'view_batches', 'enroll_students', 'student-list',
            // Certificate access for faculty
            'certificate-list', 'certificate-download', 'certificate-approve-faculty'
        ]);

        // Student gets basic specific access
        $roleStudent = Role::findByName('Student');
        $roleStudent->syncPermissions([
            'enrollment-view', 'certificate-download'
        ]);

        $this->command->info('Roles and Permissions seeded successfully.');
    }
}