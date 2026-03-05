<?php

namespace App\Policies;

use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EnrollmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('staff');
    }

    public function view(User $user, Enrollment $enrollment): bool
    {
        return $user->hasRole('admin') || $user->student?->id === $enrollment->student_id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('staff');
    }

    public function update(User $user, Enrollment $enrollment): bool
    {
        return $user->hasRole('admin') || $user->hasRole('staff');
    }

    public function delete(User $user, Enrollment $enrollment): bool
    {
        return $user->hasRole('admin');
    }
}