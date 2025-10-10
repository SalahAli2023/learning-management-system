<?php

namespace App\Listeners;

use App\Events\CoursePublished;
use App\Notifications\CoursePublished as CoursePublishedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendCoursePublishedNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CoursePublished $event): void
    {
        $course = $event->course;
        
        // Send notification to all students (or specific user groups)
        $students = User::where('role', 'student')->get();
        
        foreach ($students as $student) {
            $student->notify(new CoursePublishedNotification($course));
        }
    }
}
