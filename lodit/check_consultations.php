<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

// Check consultations in database
$consultations = DB::table('consultations')->get();
echo "Total consultations in DB: " . count($consultations) . "\n\n";

foreach ($consultations as $c) {
    echo "ID: " . $c->id . "\n";
    echo "  User ID: " . $c->user_id . "\n";
    echo "  Question: " . substr($c->question, 0, 50) . "...\n";
    echo "  Status: " . $c->status . "\n";
    echo "  Response: " . ($c->response ? substr($c->response, 0, 50) . "..." : "NONE") . "\n";
    echo "  Consultant ID: " . $c->consultant_id . "\n";
    echo "  Answered At: " . $c->answered_at . "\n";
    echo "---\n";
}
