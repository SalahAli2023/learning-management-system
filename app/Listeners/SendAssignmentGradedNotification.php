<?php

namespace App\Listeners;

use App\Events\AssignmentGraded;
use App\Notifications\AssignmentGraded as AssignmentGradedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendAssignmentGradedNotification implements ShouldQueue
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
    public function handle(AssignmentGraded $event): void
    {
        $submission = $event->submission;
        $student = $submission->student;
        
        // Send notification to student
        $student->notify(new AssignmentGradedNotification($event->assignment, $submission));
    }
}
