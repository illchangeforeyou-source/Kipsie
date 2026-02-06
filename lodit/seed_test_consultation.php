<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

try {
    // Seed a test consultation from user ID 16 (Van - customer level 1)
    $user = DB::table('login')->where('id', 16)->first();
    if (!$user) {
        echo "Error: No user with ID 16 found.\n";
        exit(1);
    }

    // Insert a test consultation
    $consultationId = DB::table('consultations')->insertGetId([
        'user_id' => 16,
        'question' => 'Is it safe to take paracetamol with my blood pressure medication?',
        'medicine_id' => null,
        'status' => 'pending',
        'consultant_id' => null,
        'response' => null,
        'answered_at' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    echo "✓ Test consultation seeded successfully!\n";
    echo "  Consultation ID: " . $consultationId . "\n";
    echo "  User: " . $user->username . " (ID: " . $user->id . ")\n";
    echo "  Status: pending\n";
    echo "  Visit /consultation/my-questions as user 16 (Van) to see it.\n";
    echo "  Visit /admin/pending-consultations as a pharmacist (level 6) or super admin (level 4) to answer it.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
