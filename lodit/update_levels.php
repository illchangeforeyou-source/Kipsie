<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$kernel->handle($request = Illuminate\Http\Request::capture());

DB::table('login')->where('username', 'Van')->update(['level' => 2]);
DB::table('login')->where('username', 'Chips')->update(['level' => 3]);
DB::table('login')->where('username', 'Kaii')->update(['level' => 4]);

echo "Levels updated:\nVan: 2\nChips: 3\nKaii: 4";
