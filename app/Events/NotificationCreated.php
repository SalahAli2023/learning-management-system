<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;
    public $userId;

    public function __construct($notification, $userId)
    {
        $this->notification = $notification;
        $this->userId = $userId;
        
        // نحذف البيانات الحساسة
        unset($this->notification->notifiable_type);
        unset($this->notification->notifiable_id);
    }

    public function broadcastOn()
    {
        return new PrivateChannel('notifications.' . $this->userId);
    }

    public function broadcastAs()
    {
        return 'notification.created';
    }

    public function broadcastWith()
    {
        return [
            'notification' => $this->notification,
            'unread_count' => \Illuminate\Support\Facades\DB::table('notifications')
                ->where('user_id', $this->userId)
                ->whereNull('read_at')
                ->count()
        ];
    }
}