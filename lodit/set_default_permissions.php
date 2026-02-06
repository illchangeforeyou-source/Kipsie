<?php
// Load Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$response = $kernel->handle(
    $request = \Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\DB;

// Give Level 1 (Customer) permission to view orders and medicines
$level1Users = [16, 21];
$readPerms = ['view_medicines', 'view_orders', 'view_consultations'];
DB::table('user_permissions')->whereIn('user_id', $level1Users)->delete();
foreach ($level1Users as $userId) {
    foreach ($readPerms as $perm) {
        DB::table('user_permissions')->insert([
            'user_id' => $userId,
            'permission_key' => $perm,
            'can_access' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
echo "Level 1: Added " . (count($level1Users) * count($readPerms)) . " permissions\n";

// Give Level 2 (Manager) more permissions
$level2Users = [22, 25];
$managerPerms = ['view_medicines', 'view_orders', 'view_users', 'view_sales_report', 'view_consultations'];
DB::table('user_permissions')->whereIn('user_id', $level2Users)->delete();
foreach ($level2Users as $userId) {
    foreach ($managerPerms as $perm) {
        DB::table('user_permissions')->insert([
            'user_id' => $userId,
            'permission_key' => $perm,
            'can_access' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
echo "Level 2: Added " . (count($level2Users) * count($managerPerms)) . " permissions\n";

// Give Level 3 (Pharmacist) most permissions except user management
$level3Users = [17, 23];
$pharmacistPerms = ['view_medicines', 'create_medicine', 'edit_medicine', 'delete_medicine', 'view_orders', 'view_sales_report', 'view_stock_report', 'view_consultations'];
DB::table('user_permissions')->whereIn('user_id', $level3Users)->delete();
foreach ($level3Users as $userId) {
    foreach ($pharmacistPerms as $perm) {
        DB::table('user_permissions')->insert([
            'user_id' => $userId,
            'permission_key' => $perm,
            'can_access' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
echo "Level 3: Added " . (count($level3Users) * count($pharmacistPerms)) . " permissions\n";

echo "\nDefault permissions set successfully!\n";
