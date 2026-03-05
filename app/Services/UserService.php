<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

class UserService
{
    public function getPaginatedUsers(?string $search, int $perPage = 10): LengthAwarePaginator
    {
        return User::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%")
                      ->orWhere('phone', 'LIKE', "%{$search}%");
                });
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function createUser(array $data): User
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        if (isset($data['avatar']) && $data['avatar'] instanceof UploadedFile) {
            $data['avatar'] = $data['avatar']->store('avatars', 'public');
        }

        $user = User::create($data);

        if (isset($data['roles'])) {
            $user->assignRole($data['roles']);
        }

        // ✅ LOG ACTIVITY
        activity()
            ->useLog('user-management')
            ->performedOn($user)
            ->causedBy(Auth::user())
            ->withProperties(['roles' => $data['roles'] ?? []])
            ->log('Created new user: ' . $user->name);

        return $user;
    }

    public function updateUser(User $user, array $data): User
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            $data = Arr::except($data, ['password']);
        }

        if (isset($data['avatar']) && $data['avatar'] instanceof UploadedFile) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $data['avatar']->store('avatars', 'public');
        }

        $originalData = $user->getOriginal(); // Capture old state for logging
        $user->update($data);

        if (isset($data['roles'])) {
            $user->syncRoles($data['roles']);
        }

        // ✅ LOG ACTIVITY
        activity()
            ->useLog('user-management')
            ->performedOn($user)
            ->causedBy(Auth::user())
            ->withProperties([
                'old' => Arr::only($originalData, array_keys($data)),
                'new' => $data
            ])
            ->log('Updated user profile: ' . $user->name);

        return $user;
    }

    public function deleteUser(User $user): void
    {
        if ($user->hasRole(config('rbac.super_admin'))) {
            throw new \Exception('Cannot delete the Super Admin account.');
        }

        // ✅ LOG ACTIVITY (Before deletion so we still have the model)
        activity()
            ->useLog('user-management')
            ->performedOn($user)
            ->causedBy(Auth::user())
            ->log('Deleted user account: ' . $user->name);

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();
    }
}