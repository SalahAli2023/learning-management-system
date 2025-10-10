<?php

namespace App\Listeners;

use App\Events\AssignmentCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\NewAssignment;
use Illuminate\Support\Facades\Log;

class SendNewAssignmentNotification implements ShouldQueue
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
    public function handle(AssignmentCreated $event): void
    {
        
        // تحقق من عدم تكرار الإشعارات
        if (cache()->has('processing_assignment_' . $event->assignment->id)) {
            return;
        }
        
        cache()->put('processing_assignment_' . $event->assignment->id, true, 60); // 60 ثانية
        try {
            $assignment = $event->assignment;
            
            // تحميل العلاقات بشكل انتقائي
            $assignment->load([
                'course.students:id,name,email,notification_preferences,email_notifications',
                'course.instructor:id,name'
            ]);

            $students = $assignment->course->enrolledStudents ?? collect();

            foreach ($students as $student) {
                // تأخير بسيط بين الإشعارات
                usleep(100000); // 0.1 ثانية
                
                $student->notify(new NewAssignment($assignment));
            }
            
        } catch (\Exception $e) {
            \Log::error('Assignment notification failed: ' . $e->getMessage());
        } finally {
            cache()->forget('processing_assignment_' . $event->assignment->id);
        }

        // try {
        //     $assignment = $event->assignment;

        //     // تحميل العلاقات المطلوبة
        //     $assignment->load(['course.students', 'course.instructor']);
            
        //     // $students = $assignment->course->students;
        //     $students = $assignment->course->students?? collect();

        //     Log::info('Sending assignment notifications', [
        //         'assignment_id' => $assignment->id,
        //         'students_count' => $students->count()
        //     ]);

        //     foreach ($students as $student) {
                
        //         // $student->notify(new NewAssignment($assignment));
        //         try {
        //             $student->notify(new NewAssignment($assignment));
        //             Log::info('Notification sent to student', [
        //                 'student_id' => $student->id,
        //                 'assignment_id' => $assignment->id
        //             ]);
        //         } catch (\Exception $e) {
        //             Log::error('Failed to send notification to student', [
        //                 'student_id' => $student->id,
        //                 'error' => $e->getMessage()
        //             ]);
        //         }
        //     }
            
        // } catch (\Exception $e) {
        //     Log::error('Assignment notification failed', [
        //         'assignment_id' => $event->assignment->id ?? 'unknown',
        //         'error' => $e->getMessage(),
        //         'trace' => $e->getTraceAsString()
        //     ]);
        // }
    }
}
