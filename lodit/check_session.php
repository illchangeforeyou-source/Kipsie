<?php
session_start();

// Check if session has user info
echo "Session ID: " . session_id() . "\n";
echo "Session vars:\n";
var_dump($_SESSION);
