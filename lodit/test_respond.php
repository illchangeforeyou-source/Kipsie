<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

// Simulate a pharmacist responding to a consultation
$consultationId = 1; // The test question we seeded

$updated = DB::table('consultations')
    ->where('id', $consultationId)
    ->update([
        'response' => 'Yes, paracetamol is generally safe with most blood pressure medications. However, please consult your doctor to ensure there are no specific interactions with your medications.',
        'consultant_id' => 17, // Chips (pharmacist)
        'status' => 'answered',
        'answered_at' => now(),
        'updated_at' => now(),
    ]);

echo "Updated: " . $updated . " row(s)\n";

// Now check if it saved
$consultation = DB::table('consultations')->where('id', $consultationId)->first();
echo "\nConsultation ID: " . $consultation->id . "\n";
echo "Status: " . $consultation->status . "\n";
echo "Response: " . $consultation->response . "\n";
echo "Consultant ID: " . $consultation->consultant_id . "\n";
echo "Answered At: " . $consultation->answered_at . "\n";
