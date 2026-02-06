<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// Check existing users
$existingUsers = DB::table('login')->select('id', 'name', 'email', 'level')->get();

echo "Existing users in database:\n";
foreach ($existingUsers as $user) {
    echo "- {$user->name} ({$user->email}) - Level {$user->level}\n";
}

echo "\n\nAdding test users...\n";

// Create test users if they don't exist
$testUsers = [
    ['name' => 'John Employee', 'email' => 'john@pharmacy.test', 'level' => 2],
    ['name' => 'Sarah Pharmacist', 'email' => 'sarah@pharmacy.test', 'level' => 3],
    ['name' => 'Admin User', 'email' => 'admin@pharmacy.test', 'level' => 4],
    ['name' => 'Mike Staff', 'email' => 'mike@pharmacy.test', 'level' => 2],
];

foreach ($testUsers as $user) {
    $exists = DB::table('login')->where('email', $user['email'])->first();
    if (!$exists) {
        DB::table('login')->insert([
            'name' => $user['name'],
            'email' => $user['email'],
            'level' => $user['level'],
            'username' => strtolower(str_replace(' ', '.', $user['name'])),
            'password' => bcrypt('password123'),
            'hidden' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "✓ Created: {$user['name']} (Level {$user['level']})\n";
    } else {
        echo "- Already exists: {$user['name']}\n";
    }
}

echo "\n\nFinal users in database:\n";
$finalUsers = DB::table('login')->select('id', 'name', 'email', 'level')->orderBy('level')->get();
foreach ($finalUsers as $user) {
    echo "- {$user->name} ({$user->email}) - Level {$user->level}\n";
}
