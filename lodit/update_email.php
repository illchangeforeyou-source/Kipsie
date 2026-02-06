<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$kernel->handle($request = Illuminate\Http\Request::capture());

DB::table('login')->where('username', 'Kaii')->update(['email' => 'kaiwivvers@gmail.com']);
echo 'Email added to Kaii';
