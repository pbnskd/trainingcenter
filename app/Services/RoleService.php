<?php

namespace App\Services;

use App\Models\Role;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class RoleService
{
    public function getPaginatedRoles(?string $search, int $perPage = 5): LengthAwarePaginator
    {
        return Role::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%");
            })
            ->orderBy('id', 'DESC')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function createRole(array $data): Role
    {
        $role = Role::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'guard_name' => 'web'
        ]);

        $permissions = [];
        if (!empty($data['permission'])) {
            $permissions = collect($data['permission'])->map(fn($id) => (int) $id)->all();
            $role->syncPermissions($permissions);
        }

        // ✅ LOG ACTIVITY
        activity()
            ->useLog('role-management')
            ->performedOn($role)
            ->causedBy(Auth::user())
            ->withProperties(['permissions_count' => count($permissions)])
            ->log('Created new role: ' . $role->name);

        return $role;
    }

    public function updateRole(Role $role, array $data): Role
    {
        $role->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        $permissions = isset($data['permission']) 
            ? collect($data['permission'])->map(fn($val) => (int)$val)->all() 
            : [];
            
        $role->syncPermissions($permissions);

        // ✅ LOG ACTIVITY
        activity()
            ->useLog('role-management')
            ->performedOn($role)
            ->causedBy(Auth::user())
            ->withProperties(['new_permissions_count' => count($permissions)])
            ->log('Updated role definition: ' . $role->name);

        return $role;
    }

    public function deleteRole(Role $role): void
    {
        if ($role->name === config('rbac.super_admin')) {
            abort(403, 'CANNOT DELETE SUPER ADMIN ROLE');
        }
        
        // ✅ LOG ACTIVITY
        activity()
            ->useLog('role-management')
            ->performedOn($role)
            ->causedBy(Auth::user())
            ->log('Deleted role: ' . $role->name);

        $role->delete();
    }
}