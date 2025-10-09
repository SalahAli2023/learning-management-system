<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DeadlineReminder extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Assignment $assignment)
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
            ($notifiable->notification_preferences['deadline_reminders'] ?? true)) {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $dueDate = $this->assignment->due_date->format('F j, Y \\a\\t g:i A');
        $daysLeft = now()->diffInDays($this->assignment->due_date, false);
        
        $subject = '⏰ Deadline Reminder: ' . $this->assignment->title;
        $message = new MailMessage();
        $message->subject($subject)
                ->greeting('Reminder: ' . $notifiable->name)
                ->line('This is a friendly reminder about an upcoming assignment deadline:')
                ->line('**Assignment:** ' . $this->assignment->title)
                ->line('**Course:** ' . $this->assignment->course->title)
                ->line('**Due Date:** ' . $dueDate);
        
        if ($daysLeft > 0) {
            $message->line('**Time Left:** ' . $daysLeft . ' day' . ($daysLeft > 1 ? 's' : ''));
        } else {
            $message->line('**Status:** Due today!');
        }
        
        $message->action('View Assignment', url("/assignments/{$this->assignment->id}"))
                ->line('Please make sure to submit your work on time!');

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $dueDate = $this->assignment->due_date;
        $daysLeft = now()->diffInDays($dueDate, false);
        
        if ($daysLeft > 0) {
            $message = 'Assignment due in ' . $daysLeft . ' day' . ($daysLeft > 1 ? 's' : '') . ': ' . $this->assignment->title;
        } else if ($daysLeft == 0) {
            $message = 'Assignment due today: ' . $this->assignment->title;
        } else {
            $message = 'Assignment overdue: ' . $this->assignment->title;
        }

        return [
            'type' => 'deadline_reminder',
            'assignment_id' => $this->assignment->id,
            'assignment_title' => $this->assignment->title,
            'course_name' => $this->assignment->course->title,
            'due_date' => $dueDate->toISOString(),
            'days_remaining' => $daysLeft,
            'message' => $message,
            'icon' => '⏰',
            'url' => "/assignments/{$this->assignment->id}",
            'priority' => $daysLeft <= 1 ? 'high' : ($daysLeft <= 3 ? 'medium' : 'low')
        ];
    }
}
