<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    
    /**
     * Display the notifications index page.
     */
    public function index(Request $request) //: View|JsonResponse
    {
        $user = auth()->user();
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $unreadCount = $user->unreadNotifications()->count();

        // إذا كان request AJAX نرجع JSON
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'notifications' => $notifications->items(),
                'unread_count' => $unreadCount,
                'pagination' => [
                    'current_page' => $notifications->currentPage(),
                    'last_page' => $notifications->lastPage(),
                    'total' => $notifications->total(),
                    'per_page' => $notifications->perPage(),
                ]
            ]);
        }

        // لـ Blade view نرجع البيانات بشكل عادي
        return view('notifications.index', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json([
            'message' => 'Notification marked as read',
            'unread_count' => Auth::user()->unreadNotifications()->count()
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response()->json([
            'message' => 'All notifications marked as read',
            'unread_count' => 0
        ]);
    }

    /**
     * Delete a notification.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->delete();

        return response()->json([
            'message' => 'Notification deleted successfully'
        ]);
    }

    /**
     * Send a test notification (للتجربة فقط).
     */
    public function sendTestNotification(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'sometimes|string',
            'type' => 'sometimes|string'
        ]);

        $user = Auth::user();

        // نستخدم الـ Notification system بشكل صحيح
        $user->notify(new \App\Notifications\TestNotification(
            $request->input('message', 'Test notification from controller'),
            $request->input('type', 'info')
        ));

        return response()->json([
            'message' => 'Test notification sent successfully',
            'unread_count' => $user->unreadNotifications()->count()
        ]);
    }
}