<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        
        // If it's an AJAX request, return JSON
        if ($request->ajax() || $request->wantsJson()) {
            // Get notifications for the current user with read status
            $limit = $request->get('limit', 10); // Default to 10, but allow override
            $notifications = UserNotification::with('notification')
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->take($limit)
                ->get()
                ->map(function ($userNotification) {
                    return [
                        'id' => $userNotification->id,
                        'notification_id' => $userNotification->notification_id,
                        'title' => $userNotification->notification->title,
                        'message' => $userNotification->notification->message,
                        'type' => $userNotification->notification->type,
                        'is_read' => $userNotification->is_read,
                        'read_at' => $userNotification->read_at,
                        'created_at' => $userNotification->created_at->format('M d, Y'),
                        'time_ago' => $userNotification->created_at->diffForHumans(),
                    ];
                });

            // Count unread notifications
            $unreadCount = UserNotification::where('user_id', $userId)
                ->where('is_read', false)
                ->count();

            return response()->json([
                'status' => true, 
                'message' => 'Notifications retrieved successfully', 
                'data' => $notifications,
                'unread_count' => $unreadCount
            ], 200);
        }
        
        // For regular requests, return the view
        return view('notifications.index');
    }

    public function markAsRead(Request $request, $id)
    {
        $userId = Auth::id();
        
        $userNotification = UserNotification::where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$userNotification) {
            return response()->json([
                'status' => false,
                'message' => 'Notification not found'
            ], 404);
        }

        $userNotification->update([
            'is_read' => true,
            'read_at' => now()
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Notification marked as read'
        ], 200);
    }

    public function markAllAsRead()
    {
        $userId = Auth::id();
        
        UserNotification::where('user_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return response()->json([
            'status' => true,
            'message' => 'All notifications marked as read'
        ], 200);
    }
}
