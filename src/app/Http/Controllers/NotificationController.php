<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display user's notifications.
     */
    public function index()
    {
        $notifications = auth()->user()
            ->notifications()
            ->with('matching')
            ->latest()
            ->paginate(20);

        return view('frontend.notifications.index', compact('notifications'));
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        
        // Verify ownership
        if ($notification->user_id != auth()->id()) {
            abort(403);
        }

        $notification->markAsRead();

        return back()->with('success', 'Đã đánh dấu thông báo là đã đọc.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        auth()->user()
            ->notifications()
            ->unread()
            ->update(['is_read' => true]);

        return back()->with('success', 'Đã đánh dấu tất cả thông báo là đã đọc.');
    }

    /**
     * Get unread notifications count (API).
     */
    public function unreadCount()
    {
        $count = auth()->user()->unreadNotificationsCount();
        
        return response()->json(['count' => $count]);
    }
}
