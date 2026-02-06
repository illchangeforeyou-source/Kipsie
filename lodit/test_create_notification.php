<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Order;
use App\Models\Notification;

echo "Testing Notification Creation\n";
echo "=============================\n\n";

// Simulate admin session
Session::put('id', 24);
Session::put('level', 3);

try {
    // Get an order to update
    $order = Order::find(13);
    
    if (!$order) {
        echo "✗ Order not found\n";
        exit;
    }
    
    echo "Order #{$order->id}: user_id={$order->user_id}, delivery_status={$order->delivery_status}\n\n";
    
    // Update delivery status to 'delivered'
    $order->delivery_status = 'delivered';
    $order->delivered_at = now();
    $order->save();
    
    echo "✓ Updated order delivery_status to 'delivered'\n";
    
    // Create notification
    $notif = Notification::create([
        'user_id' => $order->user_id,
        'title' => 'Order Delivered',
        'message' => "Your order #{$order->id} has been delivered. Thank you for shopping with us!",
        'type' => 'delivery',
        'order_id' => $order->id,
        'read' => false,
        'read_at' => null,
    ]);
    
    echo "✓ Notification created (ID: {$notif->id})\n\n";
    
    // Verify it exists
    $check = Notification::where('user_id', $order->user_id)->where('order_id', $order->id)->first();
    
    if ($check) {
        echo "Notification Details:\n";
        echo "  - ID: {$check->id}\n";
        echo "  - User ID: {$check->user_id}\n";
        echo "  - Title: {$check->title}\n";
        echo "  - Message: {$check->message}\n";
        echo "  - Order ID: {$check->order_id}\n";
        echo "  - Read: " . ($check->read ? "YES" : "NO") . "\n";
        echo "  - Created: {$check->created_at}\n\n";
        
        echo "✓ SUCCESS: Notification can be created and retrieved!\n";
    }
    
} catch (Exception $e) {
    echo "✗ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
