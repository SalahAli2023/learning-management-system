<?php

namespace App\Notifications;

use App\Models\Assignment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssignmentSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Assignment $assignment, public User $student)
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
            ($notifiable->notification_preferences['assignment_submitted'] ?? true)) {
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
                    ->subject('📝 Assignment Submitted: ' . $this->assignment->title)
                    ->greeting('Hello ' . $notifiable->name)
                    ->line('A student has submitted the assignment: ' . $this->assignment->title)
                    ->line('Student: ' . $this->student->name)
                    ->line('Course: ' . $this->assignment->course->title)
                    ->action('Review Submission', url("/assignments/{$this->assignment->id}/submissions"))
                    ->line('Thank you for using our learning platform!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'assignment_submitted',
            'assignment_id' => $this->assignment->id,
            'assignment_title' => $this->assignment->title,
            'student_id' => $this->student->id,
            'student_name' => $this->student->name,
            'course_name' => $this->assignment->course->title,
            'message' => $this->student->name . ' submitted assignment: ' . $this->assignment->title,
            'icon' => '📝',
            'url' => "/assignments/{$this->assignment->id}/submissions"
        ];
    }
}
