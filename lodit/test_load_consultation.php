<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());

use App\Models\Consultation;

// Fetch consultation 1 with relationships
$consultation = Consultation::with('medicine', 'consultant')->find(1);

echo "Consultation ID: " . $consultation->id . "\n";
echo "Question: " . $consultation->question . "\n";
echo "Status: " . $consultation->status . "\n";
echo "Response: " . ($consultation->response ?? 'NULL') . "\n";
echo "Consultant ID: " . ($consultation->consultant_id ?? 'NULL') . "\n";
echo "Consultant Name: " . ($consultation->consultant->username ?? 'NULL') . "\n";
echo "Answered At: " . ($consultation->answered_at ? $consultation->answered_at->diffForHumans() : 'NULL') . "\n";
