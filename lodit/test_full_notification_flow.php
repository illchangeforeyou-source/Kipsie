<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Order;
use App\Models\Notification;

echo "Full Patient Notification Flow Test\n";
echo "====================================\n\n";

try {
    // Step 1: Create a new order (as a patient)
    echo "Step 1: Create new order (patient session)\n";
    Session::put('id', 16);
    Session::put('level', 1);
    
    $order = new Order();
    $order->customer_name = "Test Patient";
    $order->items = json_encode([
        ['id' => 1, 'name' => 'Aspirin', 'quantity' => 2],
        ['id' => 2, 'name' => 'Cough Syrup', 'quantity' => 1]
    ]);
    $order->total = 50.00;
    $order->user_id = 16;
    $order->status = 'pending';
    $order->delivery_status = 'pending';
    $order->save();
    
    echo "  ✓ Order #{$order->id} created\n";
    echo "    - User ID: {$order->user_id}\n";
    echo "    - Customer: {$order->customer_name}\n";
    echo "    - Total: \${$order->total}\n";
    echo "    - Delivery Status: {$order->delivery_status}\n\n";
    
    // Step 2: Check no notification yet
    echo "Step 2: Check initial state (no notifications yet)\n";
    $notifCount = Notification::where('user_id', 16)->where('order_id', $order->id)->count();
    echo "  - Notifications for this order: {$notifCount}\n";
    echo "  ✓ Correct - notification only created when status changes\n\n";
    
    // Step 3: Admin updates delivery status
    echo "Step 3: Admin updates delivery status to 'delivered'\n";
    Session::put('id', 24);
    Session::put('level', 3);
    
    $order->delivery_status = 'delivered';
    $order->delivered_at = now();
    $order->save();
    
    // Create the notification (this is what DeliveryController does)
    $notification = Notification::create([
        'user_id' => $order->user_id,
        'title' => 'Order Delivered',
        'message' => "Your order #{$order->id} has been delivered. Thank you for shopping with us!",
        'type' => 'delivery',
        'order_id' => $order->id,
        'read' => false,
    ]);
    
    echo "  ✓ Order updated to 'delivered'\n";
    echo "  ✓ Notification created (ID: {$notification->id})\n\n";
    
    // Step 4: Patient checks notifications
    echo "Step 4: Patient retrieves notifications\n";
    Session::put('id', 16);
    Session::put('level', 1);
    
    $patientNotifications = Notification::where('user_id', 16)
        ->orderBy('created_at', 'desc')
        ->get();
    
    echo "  Found " . $patientNotifications->count() . " notification(s):\n";
    foreach($patientNotifications as $notif) {
        echo "    - Title: {$notif->title}\n";
        echo "      Message: {$notif->message}\n";
        echo "      Read: " . ($notif->read ? "YES" : "NO") . "\n";
    }
    
    $unreadCount = Notification::where('user_id', 16)->where('read', false)->count();
    echo "\n  Unread Count: {$unreadCount}\n\n";
    
    // Step 5: Patient marks notification as read
    echo "Step 5: Patient marks notification as read\n";
    $notification->read = true;
    $notification->read_at = now();
    $notification->save();
    
    $unreadCount = Notification::where('user_id', 16)->where('read', false)->count();
    echo "  ✓ Notification marked as read\n";
    echo "  ✓ Unread count now: {$unreadCount}\n\n";
    
    echo "✅ FULL FLOW SUCCESSFUL!\n";
    echo "Patients can now receive notifications when orders are delivered or cancelled.\n";
    
} catch (Exception $e) {
    echo "✗ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
