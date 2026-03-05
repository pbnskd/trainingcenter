<?php

namespace App\Services;

use App\Models\Permission;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class PermissionService
{
    public function getPaginatedPermissions(?string $search, int $perPage = 10): LengthAwarePaginator
    {
        return Permission::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%");
            })
            ->orderBy('id', 'DESC')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function createPermission(array $data): Permission
    {
        $permission = Permission::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'guard_name' => 'web'
        ]);

        // ✅ LOG ACTIVITY
        activity()
            ->useLog('permission-management')
            ->performedOn($permission)
            ->causedBy(Auth::user())
            ->log('Registered new permission node: ' . $permission->name);

        return $permission;
    }

    public function updatePermission(Permission $permission, array $data): Permission
    {
        $permission->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        // ✅ LOG ACTIVITY
        activity()
            ->useLog('permission-management')
            ->performedOn($permission)
            ->causedBy(Auth::user())
            ->log('Recalibrated permission node: ' . $permission->name);

        return $permission;
    }

    public function deletePermission(Permission $permission): void
    {
        // ✅ LOG ACTIVITY
        activity()
            ->useLog('permission-management')
            ->performedOn($permission)
            ->causedBy(Auth::user())
            ->log('Removed permission node: ' . $permission->name);

        $permission->delete();
    }
}