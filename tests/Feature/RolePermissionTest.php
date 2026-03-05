<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();

        Permission::create(['name' => 'role-list']);
        Permission::create(['name' => 'role-create']);
        Permission::create(['name' => 'role-delete']);
    }

    public function test_admin_can_create_role()
    {
        $admin = User::factory()->create();
        $role = Role::create(['name' => 'Admin']);
        $role->givePermissionTo('role-create');
        $admin->assignRole('Admin');

        $response = $this->actingAs($admin)->post(route('roles.store'), [
            'name' => 'Manager',
            'permission' => [] 
        ]);

        $response->assertRedirect(route('roles.index'));
        $this->assertDatabaseHas('roles', ['name' => 'Manager']);
    }

    public function test_user_cannot_delete_super_admin_role()
    {
        // Setup Super Admin Role as per config
        $superAdminName = config('rbac.super_admin', 'Super Admin');
        $superAdminRole = Role::create(['name' => $superAdminName]);

        $admin = User::factory()->create();
        $role = Role::create(['name' => 'Admin']);
        $role->givePermissionTo('role-delete');
        $admin->assignRole('Admin');

        $response = $this->actingAs($admin)->delete(route('roles.destroy', $superAdminRole->id));

        // Depending on implementation, this might be 403 or a redirect with error
        // Given our Service throws abort(403), we expect 403
        $response->assertStatus(403);
        
        $this->assertDatabaseHas('roles', ['name' => $superAdminName]);
    }
}