<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->handle(
    $request = Illuminate\Http\Request::capture()
);

$columns = DB::select("DESCRIBE login");
foreach ($columns as $col) {
    echo $col->Field . " - " . $col->Type . "\n";
}
