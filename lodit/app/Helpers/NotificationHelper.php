<?php

namespace App\Helpers;

use App\Models\Notification;
use Illuminate\Support\Facades\DB;

class NotificationHelper
{
    /**
     * Send notification to all admins and pharmacists when an order is placed
     */
    public static function notifyOrderPlaced($orderId, $customerName, $total, $itemCount)
    {
        // Get all admin and pharmacist users (levels 3, 4, 5, 6)
        $recipients = DB::table('login')
            ->whereIn('level', [3, 4, 5, 6])
            ->where('hidden', '!=', 1)
            ->pluck('id');

        $message = "$customerName ordered $itemCount medicine(s) for \$$total";
        $title = "New Order #$orderId";

        foreach ($recipients as $recipientId) {
            Notification::create([
                'user_id' => $recipientId,
                'title' => $title,
                'message' => $message,
                'type' => 'order',
                'order_id' => $orderId,
                'read' => false
            ]);
        }

        return true;
    }

    /**
     * Send notification to specific user
     */
    public static function notify($userId, $title, $message, $type = 'info', $orderId = null)
    {
        return Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'order_id' => $orderId,
            'read' => false
        ]);
    }

    /**
     * Send notification to multiple users
     */
    public static function notifyMultiple($userIds, $title, $message, $type = 'info', $orderId = null)
    {
        $notifications = [];
        
        foreach ($userIds as $userId) {
            $notifications[] = [
                'user_id' => $userId,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'order_id' => $orderId,
                'read' => false,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        return Notification::insert($notifications);
    }

    /**
     * Get unread notifications for a user
     */
    public static function getUnreadNotifications($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('read', false)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get unread count for user
     */
    public static function getUnreadCount($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('read', false)
            ->count();
    }

    /**
     * Mark notification as read
     */
    public static function markAsRead($notificationId)
    {
        $notification = Notification::find($notificationId);
        if ($notification) {
            $notification->markAsRead();
            return true;
        }
        return false;
    }

    /**
     * Mark all as read for user
     */
    public static function markAllAsRead($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('read', false)
            ->update([
                'read' => true,
                'read_at' => now()
            ]);
    }
}
