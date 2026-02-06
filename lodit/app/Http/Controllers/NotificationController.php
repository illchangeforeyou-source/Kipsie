<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Helpers\NotificationHelper;

class NotificationController extends Controller
{
    public function list(Request $request)
    {
        $userId = $request->session()->get('id');
        $userLevel = $request->session()->get('level');
        
        if (!$userId) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'notifications' => [],
                    'unread_count' => 0
                ], 401);
            }

            return redirect('/login');
        }

        $query = Notification::where('user_id', $userId);

        // If user is a pharmacist (levels 4, 5, 6), show consultation and order notifications
        if (in_array($userLevel, [4, 5, 6])) {
            $query->whereIn('type', ['consultation', 'order']);
        }

        $notifications = $query->orderBy('created_at', 'desc')->get();

        // If this is an AJAX request, return JSON for the JavaScript fetch in the blade
        if ($request->ajax()) {
            return response()->json([
                'notifications' => $notifications,
                'unread_count' => NotificationHelper::getUnreadCount($userId)
            ]);
        }

        // Otherwise return the Blade view
        return view('notifications.list', [
            'notifications' => $notifications,
            'unread_count' => NotificationHelper::getUnreadCount($userId)
        ]);
    }

    public function markAsRead(Request $request, $id)
    {
        $userId = $request->session()->get('id');
        
        $notification = Notification::find($id);
        
        if (!$notification || $notification->user_id != $userId) {
            return response()->json(['success' => false], 403);
        }

        NotificationHelper::markAsRead($id);

        return response()->json([
            'success' => true,
            'unread_count' => NotificationHelper::getUnreadCount($userId)
        ]);
    }

    public function markAllAsRead(Request $request)
    {
        $userId = $request->session()->get('id');
        
        if (!$userId) {
            return response()->json(['success' => false], 401);
        }

        NotificationHelper::markAllAsRead($userId);

        return response()->json([
            'success' => true,
            'unread_count' => 0
        ]);
    }

    /**
     * Seed a test notification for the current user (dev helper)
     */
    public function seedTest(Request $request)
    {
        $userId = $request->session()->get('id');

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Not authenticated'], 401);
        }

        $notif = Notification::create([
            'user_id' => $userId,
            'title' => 'Test Notification',
            'message' => 'This is a test notification generated for debugging.',
            'type' => 'info',
            'order_id' => null,
            'read' => false
        ]);

        return response()->json(['success' => true, 'notification' => $notif]);
    }

    public function delete(Request $request, $id)
    {
        $userId = $request->session()->get('id');
        
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $notification = Notification::find($id);
        
        if (!$notification || $notification->user_id != $userId) {
            return response()->json(['success' => false, 'message' => 'Notification not found'], 403);
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted'
        ]);
    }}