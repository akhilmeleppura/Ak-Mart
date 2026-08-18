<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class SystemNotificationController extends Controller
{
    /**
     * Display the notification hub.
     */
    public function index()
    {
        $notifications = auth()->user()->notifications()->paginate(20);
        return view('content.apps.notifications', compact('notifications'));
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        
        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all as read.
     */
    public function markAllAsRead()
    {
        if (auth()->check()) {
            auth()->user()->unreadNotifications->markAsRead();
        }
        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Alias for markAllAsRead.
     */
    public function markAllRead()
    {
        return $this->markAllAsRead();
    }
}
