<?php

namespace App\Policies;

use App\Models\Permission;
use App\Models\User;

class PermissionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('permission-list');
    }

    public function view(User $user, Permission $permission): bool
    {
        return $user->checkPermissionTo('permission-list');
    }

    public function create(User $user): bool
    {
        return $user->checkPermissionTo('permission-create');
    }

    public function update(User $user, Permission $permission): bool
    {
        return $user->checkPermissionTo('permission-edit');
    }

    public function delete(User $user, Permission $permission): bool
    {
        return $user->checkPermissionTo('permission-delete');
    }
}