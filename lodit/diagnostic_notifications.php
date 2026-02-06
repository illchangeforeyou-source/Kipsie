<?php
/**
 * Diagnostic script to debug notification system
 * 
 * Checks:
 * 1. If notifications table exists
 * 2. If orders table has user_id
 * 3. If delivery status changes are being recorded
 * 4. If notifications are being created
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Notification;

echo "═══════════════════════════════════════════════════════════════\n";
echo "PATIENT NOTIFICATION SYSTEM - DIAGNOSTIC REPORT\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// Check 1: Database tables exist
echo "CHECK 1: Database Tables\n";
echo "─────────────────────────────────────────────────────────────\n";

try {
    $tables = DB::select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE()");
    $tableNames = array_map(fn($t) => $t->TABLE_NAME, $tables);
    
    echo "✓ notifications table exists: " . (in_array('notifications', $tableNames) ? "YES\n" : "NO\n");
    echo "✓ orders table exists: " . (in_array('orders', $tableNames) ? "YES\n" : "NO\n");
    echo "✓ login table exists: " . (in_array('login', $tableNames) ? "YES\n" : "NO\n");
} catch (\Exception $e) {
    echo "✗ Error checking tables: " . $e->getMessage() . "\n";
}

echo "\n";

// Check 2: Orders table structure
echo "CHECK 2: Orders Table Structure\n";
echo "─────────────────────────────────────────────────────────────\n";

try {
    $columns = DB::select("DESCRIBE orders");
    $columnNames = array_map(fn($c) => $c->Field, $columns);
    
    echo "✓ user_id column exists: " . (in_array('user_id', $columnNames) ? "YES\n" : "NO\n");
    echo "✓ delivery_status column exists: " . (in_array('delivery_status', $columnNames) ? "YES\n" : "NO\n");
    echo "✓ delivered_at column exists: " . (in_array('delivered_at', $columnNames) ? "YES\n" : "NO\n");
    
    echo "\nAll columns in orders table:\n";
    foreach ($columnNames as $col) {
        echo "  - $col\n";
    }
} catch (\Exception $e) {
    echo "✗ Error checking orders table: " . $e->getMessage() . "\n";
}

echo "\n";

// Check 3: Recent orders
echo "CHECK 3: Recent Orders & Delivery Status\n";
echo "─────────────────────────────────────────────────────────────\n";

try {
    $orders = DB::table('orders')
        ->orderBy('updated_at', 'desc')
        ->limit(5)
        ->get();
    
    if ($orders->count() > 0) {
        echo "Found " . $orders->count() . " recent orders:\n";
        foreach ($orders as $order) {
            echo "\nOrder #" . $order->id . ":\n";
            echo "  - user_id: " . ($order->user_id ?? "NULL") . "\n";
            echo "  - customer_name: " . $order->customer_name . "\n";
            echo "  - delivery_status: " . ($order->delivery_status ?? "NULL") . "\n";
            echo "  - status: " . ($order->status ?? "NULL") . "\n";
            echo "  - delivered_at: " . ($order->delivered_at ?? "NULL") . "\n";
            echo "  - created_at: " . $order->created_at . "\n";
            echo "  - updated_at: " . $order->updated_at . "\n";
        }
    } else {
        echo "No orders found\n";
    }
} catch (\Exception $e) {
    echo "✗ Error checking orders: " . $e->getMessage() . "\n";
}

echo "\n";

// Check 4: Recent notifications
echo "CHECK 4: Recent Notifications\n";
echo "─────────────────────────────────────────────────────────────\n";

try {
    $notifications = DB::table('notifications')
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();
    
    if ($notifications->count() > 0) {
        echo "Found " . $notifications->count() . " notifications:\n";
        foreach ($notifications as $notif) {
            echo "\nNotification #" . $notif->id . ":\n";
            echo "  - user_id: " . $notif->user_id . "\n";
            echo "  - type: " . $notif->type . "\n";
            echo "  - title: " . $notif->title . "\n";
            echo "  - order_id: " . ($notif->order_id ?? "NULL") . "\n";
            echo "  - read: " . ($notif->read ? "Yes" : "No") . "\n";
            echo "  - created_at: " . $notif->created_at . "\n";
        }
    } else {
        echo "No notifications found in database\n";
    }
} catch (\Exception $e) {
    echo "✗ Error checking notifications: " . $e->getMessage() . "\n";
}

echo "\n";

// Check 5: Delivery Controller
echo "CHECK 5: DeliveryController Exists\n";
echo "─────────────────────────────────────────────────────────────\n";

$deliveryControllerPath = __DIR__ . '/app/Http/Controllers/DeliveryController.php';
if (file_exists($deliveryControllerPath)) {
    echo "✓ DeliveryController.php exists\n";
    
    $content = file_get_contents($deliveryControllerPath);
    if (strpos($content, "Notification::create") !== false) {
        echo "✓ DeliveryController creates notifications\n";
    } else {
        echo "✗ DeliveryController does NOT create notifications\n";
    }
} else {
    echo "✗ DeliveryController.php not found\n";
}

echo "\n";

// Check 6: Test a manual notification
echo "CHECK 6: Manual Notification Test\n";
echo "─────────────────────────────────────────────────────────────\n";

try {
    // Find a patient
    $patient = DB::table('login')->where('level', 1)->first();
    
    if ($patient) {
        echo "Found test patient: {$patient->username} (ID: {$patient->id})\n";
        
        // Create a test notification
        $testNotif = DB::table('notifications')->insert([
            'user_id' => $patient->id,
            'title' => 'TEST - Manual Notification',
            'message' => 'This is a test notification created at ' . now(),
            'type' => 'delivery',
            'read' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        if ($testNotif) {
            echo "✓ Successfully created test notification\n";
            
            // Try to fetch it back
            $notif = DB::table('notifications')
                ->where('user_id', $patient->id)
                ->where('type', 'delivery')
                ->orderBy('created_at', 'desc')
                ->first();
            
            if ($notif) {
                echo "✓ Test notification retrieved from database\n";
                echo "  - ID: {$notif->id}\n";
                echo "  - Title: {$notif->title}\n";
            } else {
                echo "✗ Could not retrieve test notification\n";
            }
        } else {
            echo "✗ Failed to create test notification\n";
        }
    } else {
        echo "✗ No level 1 (patient) users found\n";
    }
} catch (\Exception $e) {
    echo "✗ Error in manual test: " . $e->getMessage() . "\n";
}

echo "\n";

// Check 7: API endpoint
echo "CHECK 7: Notifications API\n";
echo "─────────────────────────────────────────────────────────────\n";

$apiPath = __DIR__ . '/app/Http/Controllers/NotificationController.php';
if (file_exists($apiPath)) {
    echo "✓ NotificationController.php exists\n";
    
    $content = file_get_contents($apiPath);
    if (strpos($content, "unread_count") !== false) {
        echo "✓ NotificationController returns unread_count\n";
    } else {
        echo "⚠ NotificationController may not return unread_count\n";
    }
} else {
    echo "✗ NotificationController.php not found\n";
}

echo "\n═══════════════════════════════════════════════════════════════\n";
echo "DIAGNOSTIC COMPLETE\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

echo "RECOMMENDATIONS:\n";
echo "───────────────────────────────────────────────────────────────\n";
echo "1. Check if orders have user_id set when created\n";
echo "2. Verify DeliveryController updateDeliveryStatus() is being called\n";
echo "3. Add logging to see if Notification::create() is executing\n";
echo "4. Check browser console for JavaScript errors\n";
echo "5. Verify /notifications/list API endpoint works\n";
echo "\n";
