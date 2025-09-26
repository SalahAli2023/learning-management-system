<?php

namespace App\Policies;

use App\Models\Lesson;
use App\Models\User;
use App\Models\Course;

use Illuminate\Auth\Access\Response;

class LessonPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Any authenticated user can view lessons list
        return $user !== null;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Lesson $lesson): bool
    {
        // الطالب المسجل في الكورس يقدر يشوف
        if ($lesson->course->students->contains($user->id)) {
            return true;
        }

        // المدرس صاحب الكورس أو الأدمن
        return $user->id === $lesson->course->instructor_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Course $course): bool
    {
        // Only instructors and admins can create lessons
        return in_array($user->role, ['instructor', 'admin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Lesson $lesson, Course $course): bool
    {
        // User can update lesson if:
        // 1. They are the course instructor
        // 2. They are an admin
        return ($user->role === 'instructor' && $user->id === $lesson->course->instructor_id) || $user->role === 'admin';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Lesson $lesson, Course $course): bool
    {
        // User can delete lesson if:
        // 1. They are the course instructor
        // 2. They are an admin
        //return $user->id === $course->instructor_id || $user->hasRole('admin');
        if ($user->role === 'admin') {
            return true;
        }

        return $user->role === 'instructor' && $lesson->course->instructor_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Lesson $lesson): bool
    {
        // User can restore lesson if:
        // 1. They are the course instructor
        // 2. They are an admin
        
        if ($user->role === 'admin') {
            return true;
        }

        return $user->role === 'instructor' && 
                $lesson->course->instructor_id === $user->id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Lesson $lesson): bool
    {
        // Only admins can permanently delete lessons
        return $user->role === 'admin';
    }
}
