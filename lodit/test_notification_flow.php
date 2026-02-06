<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Testing Notification Flow\n";
echo "==========================\n\n";

try {
    // 1. Check if we have any orders with user_id
    echo "Step 1: Check orders with user_id\n";
    $orders = DB::table('orders')
        ->where('user_id', '>', 0)
        ->orderBy('created_at', 'desc')
        ->limit(3)
        ->get();
    
    if ($orders->count() > 0) {
        foreach ($orders as $order) {
            echo "  Order #{$order->id}: user_id={$order->user_id}, delivery_status={$order->delivery_status}, created={$order->created_at}\n";
        }
    } else {
        echo "  ❌ No orders with user_id found\n";
    }
    
    // 2. Check if notifications table has any entries
    echo "\nStep 2: Check notifications in database\n";
    $notifications = DB::table('notifications')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
    
    echo "  Total notifications: " . DB::table('notifications')->count() . "\n";
    if ($notifications->count() > 0) {
        foreach ($notifications as $notif) {
            echo "    - ID: {$notif->id}, user_id: {$notif->user_id}, title: {$notif->title}, read: {$notif->read}\n";
        }
    } else {
        echo "  ❌ No notifications found\n";
    }
    
    // 3. Check if Order model has user relationship
    echo "\nStep 3: Check Order model\n";
    $order = \App\Models\Order::with('user')->first();
    if ($order) {
        echo "  Order model exists\n";
        echo "  Has user relationship: " . (method_exists($order, 'user') ? "YES" : "NO") . "\n";
        if ($order->user_id) {
            echo "  Order user_id: {$order->user_id}\n";
            $user = $order->user;
            echo "  User via relationship: " . ($user ? "FOUND" : "NULL") . "\n";
        }
    }
    
    // 4. Try to manually create a test notification
    echo "\nStep 4: Test notification creation\n";
    $testOrder = DB::table('orders')
        ->where('user_id', '>', 0)
        ->first();
    
    if ($testOrder) {
        $testNotif = DB::table('notifications')->insert([
            'user_id' => $testOrder->user_id,
            'title' => 'Test Notification',
            'message' => 'This is a test created at ' . now(),
            'type' => 'delivery',
            'order_id' => $testOrder->id,
            'read' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        echo "  ✓ Test notification inserted for user_id={$testOrder->user_id}\n";
        
        // Verify it exists
        $check = DB::table('notifications')
            ->where('user_id', $testOrder->user_id)
            ->where('title', 'Test Notification')
            ->first();
        
        if ($check) {
            echo "  ✓ Verified - notification visible in database\n";
        }
    }
    
} catch (Exception $e) {
    echo "✗ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
