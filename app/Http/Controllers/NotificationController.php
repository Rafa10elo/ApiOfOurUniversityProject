<?php

namespace App\Http\Controllers;

use App\Helpers\ApiHelper;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications;

        return ApiHelper::success("Notifications fetched", $notifications);
    }

    public function unread()
    {
        $notifications = auth()->user()->unreadNotifications;

        return ApiHelper::success("Unread notifications fetched", $notifications);
    }

    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->find($id);

        if (!$notification) {
            return ApiHelper::error("Notification not found", 404);
        }

        $notification->markAsRead();

        return ApiHelper::success("Notification marked as read");
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return ApiHelper::success("All notifications marked as read");
    }
}
