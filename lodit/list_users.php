<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

$users = DB::table('login')->select('id', 'username', 'level')->get();
echo "Users in login table:\n";
foreach($users as $user) {
    echo "ID: " . $user->id . ", Username: " . $user->username . ", Level: " . $user->level . "\n";
}
