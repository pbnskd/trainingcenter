<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class CoursePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the course catalog.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view a specific course.
     */
    public function view(?User $user, Course $course): bool
    {
        // 1. PUBLIC ACCESS:
        // Course must be 'published' AND (published_at is null OR in the past)
        $isPublished = $course->status === 'published' 
            && ($course->published_at === null || $course->published_at <= now());

        if ($isPublished) {
            return true;
        }

        // 2. ADMIN/STAFF ACCESS (Drafts/Archived):
        // User must be logged in and have permission to view drafts
        return $user?->can('view unpublished courses') ?? false;
    }

    /**
     * Determine whether the user can create courses.
     */
    public function create(User $user): bool
    {
        return $user->can('create courses');
    }

    /**
     * Determine whether the user can update the course.
     */
    public function update(User $user, Course $course): bool
    {
        // If you had a 'user_id' column, you could restore ownership check here:
        // return $user->id === $course->user_id || $user->can('edit courses');
        
        return $user->can('edit courses');
    }

    /**
     * Determine whether the user can delete the course.
     */
    public function delete(User $user, Course $course): bool
    {
        return $user->can('delete courses');
    }

    /**
     * Determine whether the user can restore the course.
     */
    public function restore(User $user, Course $course): bool
    {
        return $user->can('restore courses');
    }

    /**
     * Determine whether the user can permanently delete the course.
     */
    public function forceDelete(User $user, Course $course): bool
    {
        return $user->can('force delete courses');
    }
}