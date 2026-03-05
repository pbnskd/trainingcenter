<?php

namespace App\Providers;

// Models
use App\Models\Batch;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;

// Policies
use App\Policies\BatchPolicy;
use App\Policies\CoursePolicy;
use App\Policies\PermissionPolicy;
use App\Policies\RolePolicy;
use App\Policies\StudentPolicy;
use App\Policies\UserPolicy;
use App\Policies\EnrollmentPolicy;
use App\Policies\CertificatePolicy;

// Core Framework
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Role::class => RolePolicy::class,
        Permission::class => PermissionPolicy::class,
        Course::class => CoursePolicy::class,
        Batch::class => BatchPolicy::class,
        Student::class => StudentPolicy::class,
        Enrollment::class => EnrollmentPolicy::class,
        Certificate::class => CertificatePolicy::class, // Fixed this line
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // ----------------------------------------------------------------
        // 1. SUPER ADMIN BYPASS (GOD MODE)
        // ----------------------------------------------------------------
        // This intercepts every single permission check globally.
        Gate::before(function ($user, $ability) {
            $superAdminRole = config('rbac.super_admin', 'Super Admin');

            if ($user->hasRole($superAdminRole)) {
                return true;
            }
        });
    }
}