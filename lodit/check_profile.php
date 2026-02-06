<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\DB;

// Check login table
$results = DB::table('login')->select('id', 'profile_picture')->whereNotNull('profile_picture')->limit(3)->get();

foreach ($results as $row) {
    echo "ID: {$row->id}, Profile: {$row->profile_picture}\n";
}
