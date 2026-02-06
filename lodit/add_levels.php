<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Add missing levels
DB::table('level')->insertOrIgnore([
    ['lvlnumber' => 2, 'beingas' => 'Employee'],
    ['lvlnumber' => 3, 'beingas' => 'Admin'],
    ['lvlnumber' => 4, 'beingas' => 'Super Admin'],
    ['lvlnumber' => 5, 'beingas' => 'Cashier'],
    ['lvlnumber' => 6, 'beingas' => 'User Manager'],
    ['lvlnumber' => 7, 'beingas' => 'Pharmacist'],
]);

echo "Levels updated successfully!\n";

// Display all levels
$levels = DB::table('level')->orderBy('lvlnumber')->get();
echo "\nAll Levels:\n";
foreach($levels as $l) {
    echo "{$l->lvlnumber} - {$l->beingas}\n";
}
