<?php

namespace App\Policies;

use App\Models\Certificate;
use App\Models\User;

class CertificatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Faculty', 'Super Admin']); 
    }

    public function download(User $user, Certificate $certificate): bool
    {
        if ($user->hasAnyRole(['Admin', 'Faculty', 'Super Admin'])) {
            return true;
        }
        
        return $user->student && $user->student->id === $certificate->batchStudent->student_id;
    }

    // Notice the "?Certificate $certificate = null" addition below
    public function approveFaculty(User $user, ?Certificate $certificate = null): bool
    {
        return $user->hasAnyRole(['Faculty', 'Admin', 'Super Admin']);
    }

    public function approveAdmin(User $user, ?Certificate $certificate = null): bool
    {
        return $user->hasAnyRole(['Admin', 'Super Admin']);
    }
}