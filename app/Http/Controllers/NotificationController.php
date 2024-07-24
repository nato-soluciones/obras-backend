<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function index(): Response
    {
        $notifications = Notification::where('user_id', auth()->user()->id)
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        return response($notifications, 200);
    }

    public function notificationNewCount(): Response
    {
        $notificationCount = Notification::where('user_id', auth()->user()->id)
            ->where('is_read', false)
            ->count();
        return response($notificationCount, 200);
    }

    public function show(int $id): Response
    {
        $notification = Notification::find($id);
        return response($notification, 200);
    }

    public function markAllAsRead(): Response
    {
        $notifications = Notification::where('user_id', auth()->user()->id)->get();
        foreach ($notifications as $notification) {
            $notification->is_read = true;
            $notification->date_read = date('Y-m-d');
            $notification->save();
        }
        return response($notifications, 200);
    }

    public function markAsRead(int $id): Response
    {
        $notification = Notification::find($id);
        $notification->is_read = true;
        $notification->date_read = date('Y-m-d');
        $notification->save();
        return response($notification, 200);
    }

    public function destroy(int $id): Response
    {
        $notification = Notification::find($id);
        $notification->delete();
        return response($notification, 200);
    }
}
