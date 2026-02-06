<?php
/**
 * Fix script to update existing orders with user_id
 * This handles orders that were created before the user_id field was added
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\DB;

echo "═══════════════════════════════════════════════════════════════\n";
echo "ORDER FIX SCRIPT - Add User IDs to Orders\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

try {
    // Find orders with NULL user_id
    $ordersToFix = DB::table('orders')
        ->whereNull('user_id')
        ->orWhere('user_id', '')
        ->count();
    
    echo "Found $ordersToFix orders without user_id\n";
    
    if ($ordersToFix > 0) {
        // Get a default patient (level 1 user)
        $defaultPatient = DB::table('login')
            ->where('level', 1)
            ->where('hidden', '!=', 1)
            ->first();
        
        if ($defaultPatient) {
            echo "Using default patient: {$defaultPatient->username} (ID: {$defaultPatient->id})\n\n";
            
            // Update all orders with NULL user_id to use the default patient
            $updated = DB::table('orders')
                ->whereNull('user_id')
                ->orWhere('user_id', '')
                ->update([
                    'user_id' => $defaultPatient->id,
                    'status' => DB::raw("COALESCE(status, 'pending')"),
                    'delivery_status' => DB::raw("COALESCE(delivery_status, 'pending')"),
                ]);
            
            echo "✅ Updated $updated orders with user_id\n\n";
            
            // Show some updated orders
            $samples = DB::table('orders')
                ->where('user_id', $defaultPatient->id)
                ->orderBy('updated_at', 'desc')
                ->limit(5)
                ->get();
            
            echo "Sample updated orders:\n";
            foreach ($samples as $order) {
                echo "  - Order #" . $order->id . ": " . $order->customer_name . " (user_id: " . $order->user_id . ")\n";
            }
            
        } else {
            echo "❌ No level 1 (patient) users found!\n";
            echo "Please create a patient user first.\n";
            exit(1);
        }
    } else {
        echo "✅ All orders already have user_id set\n";
    }
    
    echo "\n═══════════════════════════════════════════════════════════════\n";
    echo "FIX COMPLETE\n";
    echo "═══════════════════════════════════════════════════════════════\n";
    echo "\nNow when admins update order status to 'Delivered' or 'Cancelled',\n";
    echo "notifications will be created for the patient.\n\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
