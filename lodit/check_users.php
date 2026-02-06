<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

$users = DB::table('login')->select('id', 'username', 'level')->limit(5)->get();
echo "Available users:\n";
foreach ($users as $u) {
    echo "  ID: " . $u->id . " | Username: " . $u->username . " | Level: " . $u->level . "\n";
}
