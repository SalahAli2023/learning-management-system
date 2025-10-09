<?php

namespace App\Notifications;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssignmentGraded extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Assignment $assignment, public AssignmentSubmission $submission)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];
        
        if ($notifiable->email_notifications && 
            ($notifiable->notification_preferences['assignment_graded'] ?? true)) {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('🎓 Assignment Graded: ' . $this->assignment->title)
                    ->greeting('Hello ' . $notifiable->name)
                    ->line('Your assignment has been graded: ' . $this->assignment->title)
                    ->line('Score: ' . $this->submission->grade . '/' . $this->assignment->total_marks)
                    ->line('Instructor Feedback: ' . ($this->submission->feedback ?? 'No feedback provided'))
                    ->action('View Grade', url("/assignments/{$this->assignment->id}/submission"))
                    ->line('Keep up the great work!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'assignment_graded',
            'assignment_id' => $this->assignment->id,
            'assignment_title' => $this->assignment->title,
            'submission_id' => $this->submission->id,
            'grade' => $this->submission->grade,
            'total_marks' => $this->assignment->total_marks,
            'feedback' => $this->submission->feedback,
            'instructor_name' => $this->submission->gradedBy->name ?? 'Instructor',
            'message' => 'Assignment graded: ' . $this->assignment->title . ' - Score: ' . $this->submission->grade . '/' . $this->assignment->total_marks,
            'icon' => '🎓',
            'url' => "/assignments/{$this->assignment->id}/submission"
        ];
    }
}
