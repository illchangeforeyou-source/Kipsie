<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

// Start a session to check what the current session has
Session::start();
$sessionId = Session::get('id');

echo "Current Session ID: " . ($sessionId ? $sessionId : "NOT SET") . "\n\n";

// Show all users in login table with their IDs
echo "All users in login table:\n";
$users = DB::table('login')->orderBy('id')->get();
foreach($users as $user) {
    echo "  - ID {$user->id}: {$user->username}\n";
}

// Show orders and their associated user_ids
echo "\n\nOrders and user associations:\n";
$orders = DB::table('orders')->get();
foreach($orders as $order) {
    $userExists = DB::table('login')->where('id', $order->user_id)->first();
    $status = $userExists ? "✓ EXISTS" : "✗ MISSING";
    echo "  Order #{$order->id}: user_id={$order->user_id} $status\n";
}
