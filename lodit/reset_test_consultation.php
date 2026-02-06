<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

// Reset consultation 1 to pending state
$updated = DB::table('consultations')
    ->where('id', 1)
    ->update([
        'response' => null,
        'consultant_id' => null,
        'status' => 'pending',
        'answered_at' => null,
        'updated_at' => now(),
    ]);

echo "Reset consultation 1\n";

// Check state
$consultation = DB::table('consultations')->where('id', 1)->first();
echo "Status: " . $consultation->status . "\n";
echo "Response: " . ($consultation->response ?? 'NULL') . "\n";
echo "Consultant ID: " . ($consultation->consultant_id ?? 'NULL') . "\n";
