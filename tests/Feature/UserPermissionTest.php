<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\PermissionRegistrar;

class UserPermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Reset cached permissions
        $this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();
        
        // Create necessary permissions
        Permission::create(['name' => 'user-list']);
        Permission::create(['name' => 'user-create']);
        Permission::create(['name' => 'user-edit']);
        Permission::create(['name' => 'user-delete']);
    }

    public function test_admin_can_view_user_list()
    {
        $admin = User::factory()->create();
        $role = Role::create(['name' => 'Admin']);
        $role->givePermissionTo('user-list');
        $admin->assignRole('Admin');

        $response = $this->actingAs($admin)->get(route('users.index'));

        $response->assertStatus(200);
    }

    public function test_unauthorized_user_cannot_view_user_list()
    {
        $user = User::factory()->create();
        // No role assigned

        $response = $this->actingAs($user)->get(route('users.index'));

        $response->assertStatus(403); // Forbidden
    }

    public function test_admin_can_create_user()
    {
        $admin = User::factory()->create();
        $role = Role::create(['name' => 'Admin']);
        $role->givePermissionTo('user-create');
        $admin->assignRole('Admin');

        // Ensure Role exists for assignment
        Role::create(['name' => 'Staff']);

        $response = $this->actingAs($admin)->post(route('users.store'), [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'roles' => ['Staff'],
            'status' => 1
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', ['email' => 'newuser@example.com']);
    }
}