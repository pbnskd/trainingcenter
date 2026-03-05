<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('student-list') || $user->hasRole('Super Admin');
    }

    public function view(User $user, Student $student): bool
    {
        return $user->checkPermissionTo('student-list') 
            || $user->id === $student->user_id // Own profile
            || $user->hasRole('Super Admin');
    }

    public function create(User $user): bool
    {
        return $user->checkPermissionTo('student-create') || $user->hasRole('Super Admin');
    }

    public function update(User $user, Student $student): bool
    {
        return $user->checkPermissionTo('student-edit') || $user->hasRole('Super Admin');
    }

    public function delete(User $user, Student $student): bool
    {
        return $user->checkPermissionTo('student-delete') || $user->hasRole('Super Admin');
    }
    public function quickCreate(User $user): bool
    {
        return $user->checkPermissionTo('student-create') || $user->hasRole('Super Admin');
    }
}