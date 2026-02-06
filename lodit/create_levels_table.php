<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

// Create a simple `levels` table if it doesn't exist and upsert level 7 and 9
try {
    DB::statement("CREATE TABLE IF NOT EXISTS `levels` (
        `id` INT NOT NULL PRIMARY KEY,
        `name` VARCHAR(191) NOT NULL,
        `created_at` TIMESTAMP NULL,
        `updated_at` TIMESTAMP NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    $now = date('Y-m-d H:i:s');

    DB::table('levels')->updateOrInsert(
        ['id' => 7],
        ['name' => 'Cashier', 'updated_at' => $now, 'created_at' => $now]
    );

    DB::table('levels')->updateOrInsert(
        ['id' => 9],
        ['name' => 'Cashier Leader', 'updated_at' => $now, 'created_at' => $now]
    );

    echo "Levels table created/updated: 7 -> Cashier, 9 -> Cashier Leader\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
