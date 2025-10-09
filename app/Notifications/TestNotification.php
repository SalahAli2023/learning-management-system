<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $message = 'Test notification',
        public string $type = 'info'
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('🧪 Test Notification - Laravel System')
                    ->greeting('Hello ' . $notifiable->name)
                    ->line($this->message)
                    ->action('View Notifications', url('/notifications'))
                    ->line('Thank you for using our application!');
    }

    public function toArray(object $notifiable): array
    {
        $icons = [
            'info' => 'ℹ️',
            'success' => '✅',
            'warning' => '⚠️',
            'error' => '❌',
        ];

        return [
            'type' => 'test_notification',
            'message' => $this->message,
            'notification_type' => $this->type,
            'icon' => $icons[$this->type] ?? '🔔',
            'url' => '/notifications',
            'sent_at' => now()->toISOString(),
        ];
    }
}