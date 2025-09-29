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
    public function viewAny(User $user, Course $course): bool
    {
        // Course instructor can view lessons
        if ($user->id === $course->instructor_id) {
            return true;
        }

        // Admin can view all lessons
        if ($user->role === 'admin') {
            return true;
        }

        // Student enrolled in the course can view lessons
        return $course->students()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Lesson $lesson): bool
    {
        // Course instructor can view
        if ($user->id === $lesson->course->instructor_id) {
            return true;
        }

        // Admin can view
        if ($user->role === 'admin') {
            return true;
        }

        // Student enrolled in the course can view if lesson is free or they have access
        if ($lesson->course->students()->where('user_id', $user->id)->exists()) {
            return $lesson->is_free || $this->hasAccessToPremiumContent($user, $lesson->course);
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Course $course): bool
    {
        // Only course instructor or admin can create lessons
        return $user->id === $course->instructor_id || $user->role === 'admin';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Lesson $lesson): bool
    {
        // Only course instructor or admin can update lessons
        return $user->id === $lesson->course->instructor_id || $user->role === 'admin';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Lesson $lesson): bool
    {
        // Only course instructor or admin can delete lessons
        return $user->id === $lesson->course->instructor_id || $user->role === 'admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Lesson $lesson): bool
    {
        // Only course instructor or admin can restore lessons
        return $user->id === $lesson->course->instructor_id || $user->role === 'admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Lesson $lesson): bool
    {
        // Only admin can permanently delete lessons
        if ($user->role === 'admin') {
            return true;
        }
    }

    /**
     * Check if user has access to premium content
     */
    private function hasAccessToPremiumContent(User $user, Course $course): bool
    {
        // Implement your logic for premium content access
        // This could check subscriptions, payments, etc.
        return $course->students()
            ->where('user_id', $user->id)
            ->where('has_access', true)
            ->exists();
    }
}