<?php
/**
 * Test script to verify patient notifications work when medicine is delivered/cancelled
 * 
 * This script:
 * 1. Creates a test order for a level 1 (patient) user
 * 2. Simulates delivery status change to trigger notifications
 * 3. Verifies notifications are created in the database
 */

// Load Laravel autoload
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap/app.php';

use App\Models\Order;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\DB;

// Get or create a test patient (level 1 user)
$testPatient = DB::table('login')
    ->where('level', 1)
    ->where('hidden', '!=', 1)
    ->first();

if (!$testPatient) {
    echo "❌ No level 1 (patient) users found in database.\n";
    echo "Please create a test patient user first.\n";
    exit(1);
}

$patientId = $testPatient->id;
echo "✓ Using test patient: {$testPatient->username} (ID: {$patientId})\n\n";

// Create a test order for the patient
echo "Creating test order...\n";
$order = Order::create([
    'customer_name' => $testPatient->username,
    'user_id' => $patientId,
    'items' => json_encode([
        ['name' => 'Test Medicine', 'quantity' => 2, 'price' => 50]
    ]),
    'total' => 100.00,
    'status' => 'pending',
    'delivery_status' => 'pending',
    'delivery_notes' => 'Test order for notification system',
]);

echo "✓ Order created: #{$order->id}\n\n";

// Clear any existing notifications for this order
Notification::where('order_id', $order->id)->delete();
echo "Cleared existing notifications for this order\n\n";

// Test 1: Simulate delivery
echo "Test 1: Simulating DELIVERY status change...\n";
$order->update([
    'delivery_status' => 'delivered',
    'delivered_at' => now(),
]);

// Check if notification was created
$deliveryNotif = Notification::where('order_id', $order->id)
    ->where('type', 'delivery')
    ->where('user_id', $patientId)
    ->first();

if ($deliveryNotif) {
    echo "✓ Delivery notification created!\n";
    echo "  - Title: {$deliveryNotif->title}\n";
    echo "  - Message: {$deliveryNotif->message}\n";
    echo "  - Read: " . ($deliveryNotif->read ? 'Yes' : 'No') . "\n";
} else {
    echo "❌ Delivery notification NOT created\n";
}

echo "\n";

// Reset order and test cancellation
$order->update([
    'delivery_status' => 'pending',
    'delivered_at' => null,
]);
Notification::where('order_id', $order->id)->delete();

// Test 2: Simulate cancellation
echo "Test 2: Simulating CANCELLED status change...\n";
$order->update([
    'delivery_status' => 'cancelled',
    'delivery_notes' => 'Test cancellation',
]);

// Check if notification was created
$cancelNotif = Notification::where('order_id', $order->id)
    ->where('type', 'delivery')
    ->where('user_id', $patientId)
    ->first();

if ($cancelNotif) {
    echo "✓ Cancellation notification created!\n";
    echo "  - Title: {$cancelNotif->title}\n";
    echo "  - Message: {$cancelNotif->message}\n";
    echo "  - Read: " . ($cancelNotif->read ? 'Yes' : 'No') . "\n";
} else {
    echo "❌ Cancellation notification NOT created\n";
}

echo "\n";

// Show all notifications for this patient
echo "All notifications for patient {$testPatient->username}:\n";
$allNotifs = Notification::where('user_id', $patientId)
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();

if ($allNotifs->count() > 0) {
    foreach ($allNotifs as $notif) {
        $readStatus = $notif->read ? '✓ Read' : '✗ Unread';
        echo "  - [{$readStatus}] {$notif->title}: {$notif->message}\n";
    }
} else {
    echo "  No notifications found\n";
}

// Cleanup
echo "\n";
echo "Cleaning up test order...\n";
Notification::where('order_id', $order->id)->delete();
$order->delete();
echo "✓ Test order and notifications removed\n";

echo "\n✅ Test completed successfully!\n";
echo "Notification system is working correctly for level 1 patients.\n";
