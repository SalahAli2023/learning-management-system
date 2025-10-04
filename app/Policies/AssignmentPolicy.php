<?php

namespace App\Policies;

use App\Models\Assignment;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class AssignmentPolicy
{
    use HandlesAuthorization;

    // للطلاب - يشوفوا الـ assignment لو هم مسجلين في الكورس
    public function viewAsStudent(User $user, Assignment $assignment)
    {
        return $user->role === 'student' && 
                $user->isEnrolledIn($assignment->course_id);
    
    }
    
     // للطلاب - يشوفوا قائمة الـ assignments للكورس
    public function viewAnyAsStudent(User $user, $course)
    {
        return $user->role === 'student';

    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user,$course): bool
    {
        return $user->role === 'instructor' && $user->id === $course->instructor_id;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Assignment $assignment): bool
    {
        return $user->role === 'instructor' && $user->id === $assignment->course->instructor_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user,$course): bool
    {
        return $user->role === 'instructor' && $user->id === $course->instructor_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Assignment $assignment): bool
    {
        return $user->role === 'instructor' && $user->id === $assignment->course->instructor_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Assignment $assignment): bool
    {
        return $user->role === 'instructor' && $user->id === $assignment->course->instructor_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Assignment $assignment): bool
    {
        return $user->role === 'instructor' && $user->id === $assignment->course->instructor_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Assignment $assignment): bool
    {
        return $user->role === 'instructor' && $user->id === $assignment->course->instructor_id;
    }
}
