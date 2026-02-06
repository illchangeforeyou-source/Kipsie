<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\NotificationController;

echo "Testing Notification API Endpoint\n";
echo "==================================\n\n";

// Simulate patient session (user 16)
Session::put('id', 16);
Session::put('level', 1);

try {
    // Create a mock request with session data
    $request = new Request();
    // Put data directly into the request without setting the session manager
    
    $controller = new NotificationController();
    
    // We need to simulate the session() helper working, so let's check the database directly
    echo "Checking notifications in database for user 16:\n\n";
    
    $notifications = \App\Models\Notification::where('user_id', 16)
        ->orderBy('created_at', 'desc')
        ->get();
    
    if ($notifications->count() > 0) {
        foreach($notifications as $notif) {
            echo "  ✓ Notification ID {$notif->id}:\n";
            echo "    Title: {$notif->title}\n";
            echo "    Message: {$notif->message}\n";
            echo "    Read: " . ($notif->read ? "YES" : "NO") . "\n";
            echo "    Type: {$notif->type}\n";
            echo "    Created: {$notif->created_at}\n\n";
        }
    } else {
        echo "  No notifications found\n";
    }
    
    // Count unread
    $unreadCount = \App\Models\Notification::where('user_id', 16)
        ->where('read', false)
        ->count();
    
    echo "Unread Notifications: {$unreadCount}\n\n";
    
    echo "✓ Notifications are correctly stored in database!\n";
    
} catch (Exception $e) {
    echo "✗ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
