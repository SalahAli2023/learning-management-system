<?php

namespace App\Notifications;

use App\Models\Assignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class NewAssignment extends Notification implements ShouldQueue
{
    use Queueable;

    public Assignment $assignment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Assignment $assignment)
    {
        $this->assignment = $assignment;
        Log::info('NewAssignment notification created', ['assignment_id' => $assignment->id]);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        Log::info('Determining notification channels for user: ' . $notifiable->id);

        $channels = ['database'];

        // تحقق من الإعدادات بشكل آمن
        $preferences = $notifiable->notification_preferences ?? ['new_assignments' => true];
        $wantsEmail = $notifiable->email_notifications && ($preferences['new_assignments'] ?? true);
        
        if ($wantsEmail) {
            $channels[] = 'mail';
            Log::info('Email channel added for user: ' . $notifiable->id);
        }

        if ($notifiable->email_notifications && 
            ($notifiable->notification_preferences['new_assignments'] ?? true)) {
            $channels[] = 'mail';
        }

        Log::info('Channels for user ' . $notifiable->id . ': ' . implode(', ', $channels));

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        Log::info('Sending email notification to: ' . $notifiable->email);

        $courseTitle = $this->assignment->course->title ?? 'Unknown Course';
        $dueDate = $this->assignment->due_date ? $this->assignment->due_date->format('Y-m-d H:i') : 'No due date';
        return (new MailMessage)
            ->subject('🎯 New Assignment : ' . $this->assignment->title)
                    ->greeting('Hello ' . $notifiable->name)
                    ->line('A new assignment has been added to the course.' . $courseTitle)
                    ->action('View assignment ', url("/courses/{$this->assignment->course_id}/assignments/{$this->assignment->id}"))
                    ->line('Assignment submission  deadline: ' . $dueDate)
                    ->line('Thank you for using our educational platform!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        Log::info('Creating database notification for user: ' . $notifiable->id);

        $courseTitle = $this->assignment->course->title ?? 'Unknown Course';
        $instructorName = $this->assignment->course->instructor->name ?? 'Unknown Instructor';
        $dueDate = $this->assignment->due_date ? $this->assignment->due_date->toISOString() : null;
        
        return [
            'type' => 'new_assignment',
            'assignment_id' => $this->assignment->id,
            'assignment_title' => $this->assignment->title,
            'course_name' => $courseTitle,
            'instructor_name' => $instructorName,
            'due_date' => $dueDate,
            'message' => 'New Assignment : ' . $this->assignment->title,
            'icon' => '🎯',
            'url' => "/courses/{$this->assignment->course_id}/assignments/{$this->assignment->id}"
        ];
    }
}
