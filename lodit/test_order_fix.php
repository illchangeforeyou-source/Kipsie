<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

try {
    echo "Testing Order Creation Fix\n";
    echo "==========================\n\n";
    
    // Set a session ID (simulating logged-in user)
    Session::start();
    Session::put('id', 1);
    
    // Test data
    $testData = [
        'customer_name' => 'Test Customer',
        'items' => json_encode([
            ['id' => 1, 'name' => 'Medicine 1', 'quantity' => 2, 'price' => 100],
            ['id' => 2, 'name' => 'Medicine 2', 'quantity' => 1, 'price' => 50]
        ]),
        'total' => 250.00,
        'user_id' => 1,
        'status' => 'pending'
    ];
    
    // Try to insert order
    echo "Attempting to insert order with data:\n";
    echo "- Customer: " . $testData['customer_name'] . "\n";
    echo "- Items: 2\n";
    echo "- Total: " . $testData['total'] . "\n";
    echo "- User ID: " . $testData['user_id'] . "\n";
    echo "- Status: " . $testData['status'] . "\n\n";
    
    $orderId = DB::table('orders')->insertGetId([
        'customer_name' => $testData['customer_name'],
        'items' => $testData['items'],
        'total' => $testData['total'],
        'user_id' => $testData['user_id'],
        'status' => $testData['status'],
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    echo "✓ SUCCESS! Order created with ID: " . $orderId . "\n\n";
    
    // Verify order was created
    $order = DB::table('orders')->where('id', $orderId)->first();
    if ($order) {
        echo "Verification:\n";
        echo "- ID: " . $order->id . "\n";
        echo "- Customer: " . $order->customer_name . "\n";
        echo "- Total: " . $order->total . "\n";
        echo "- User ID: " . $order->user_id . "\n";
        echo "- Status: " . $order->status . "\n";
        echo "- Created At: " . $order->created_at . "\n";
    }
    
} catch (Exception $e) {
    echo "✗ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
