<?php
require_once 'bootstrap/app.php';

use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

// Get the application
$app = require_once(__DIR__ . '/bootstrap/app.php');
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Simulate a request from logged-in user
$request = Request::capture();
$request->session()->put('id', 18); // Kaii, level 4
$request->session()->put('level', 4);

// Make the request to the controller
$permissionsController = new App\Http\Controllers\UserPermissionsController();
$response = $permissionsController->getLevelPermissions($request);

echo "=== API Response for Level 4 (Kaii) ===\n";
echo json_encode(json_decode($response->getContent()), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
