<?php

namespace App\Notifications;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CoursePublished extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Course $course)
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
            ($notifiable->notification_preferences['course_updates'] ?? true)) {
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
                    ->subject('🎉 New Course Published: ' . $this->course->title)
                    ->greeting('Exciting News ' . $notifiable->name)
                    ->line('A new course has been published and is now available for enrollment:')
                    ->line('**Course:** ' . $this->course->title)
                    ->line('**Instructor:** ' . $this->course->instructor->name)
                    ->line('**Description:** ' . ($this->course->description ?? 'No description available'))
                    ->action('View Course', url("/courses/{$this->course->id}"))
                    ->line('Expand your knowledge and enroll today!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'course_published',
            'course_id' => $this->course->id,
            'course_name' => $this->course->title,
            'instructor_name' => $this->course->instructor->name,
            'description' => $this->course->description,
            'message' => 'New course published: ' . $this->course->title,
            'icon' => '🎉',
            'url' => "/courses/{$this->course->id}"
        ];
    }
}
