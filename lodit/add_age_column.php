<?php

// Add age column to login table
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

// Get database connection
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->bootstrap();

$connection = \Illuminate\Support\Facades\DB::connection();

try {
    // Check if column doesn't exist
    if (!Schema::hasColumn('login', 'age')) {
        Schema::table('login', function (Blueprint $table) {
            $table->integer('age')->nullable()->after('phone');
        });
        echo "✓ Age column added successfully to login table\n";
    } else {
        echo "✓ Age column already exists\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

?>
