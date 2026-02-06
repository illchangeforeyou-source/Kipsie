<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Cleaning up invalid orders...\n";
echo "=============================\n\n";

// Delete old/invalid orders
$deletedCount = DB::table('orders')->whereIn('id', [1,2,3,4,5,6,7,8,9,10,11,12])->delete();
echo "Deleted {$deletedCount} old/invalid orders (IDs 1-12)\n";

// Delete any orders with NULL user_id
$deletedNull = DB::table('orders')->whereNull('user_id')->delete();
echo "Deleted {$deletedNull} orders with NULL user_id\n";

// Verify remaining orders
echo "\nRemaining orders:\n";
$orders = DB::table('orders')->get();
foreach($orders as $order) {
    $userExists = DB::table('login')->where('id', $order->user_id)->first();
    $status = $userExists ? "✓" : "✗";
    echo "  {$status} Order #{$order->id}: user_id={$order->user_id}, delivery_status={$order->delivery_status}\n";
}

echo "\n✓ Cleanup complete!\n";
