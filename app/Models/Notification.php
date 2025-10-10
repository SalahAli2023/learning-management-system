<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';
    protected $fillable = [
        'user_id',
        'message',
        'type',
        'status',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime'
    ];

    public function user()
    { 
        return $this->belongsTo(User::class); 
    }

    public function markAsRead()
    {
        $this->update([
            'read_at' => now()
        ]);
    }

    public function scopeUnread($query)
    {
        return $query->where('status', 'Unread');
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function getNotificationIcon()
    {
        $icons = [
            'assignment_created' => '📝',
            'assignment_graded' => '🎯',
            'course_published' => '📚',
            'enrollment_accepted' => '✅',
            'system_alert' => '⚠️',
            'message_received' => '💬'
        ];

        return $icons[$this->type] ?? '🔔';
    }

}
