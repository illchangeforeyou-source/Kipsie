<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$kernel->handle($request = Illuminate\Http\Request::capture());

DB::table('login')->where('username', 'Van')->update(['email' => 'evellolyqqe@gmail.com']);
DB::table('login')->where('username', 'Chips')->update(['email' => 'renaurelian@gmail.com']);

echo "Updated emails:\nVan -> evellolyqqe@gmail.com\nChips -> renaurelian@gmail.com\n";
