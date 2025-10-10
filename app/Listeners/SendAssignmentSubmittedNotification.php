<?php

namespace App\Listeners;

use App\Notifications\AssignmentSubmitted as AssignmentSubmittedNotification;
use App\Events\AssignmentSubmitted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendAssignmentSubmittedNotification implements ShouldQueue
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
    public function handle(AssignmentSubmitted $event): void
    {
        $assignment = $event->assignment;
        $instructor = $assignment->course->instructor;
        
        // Send notification to instructor
        $instructor->notify(new AssignmentSubmittedNotification($assignment, $event->student));
    }
}
