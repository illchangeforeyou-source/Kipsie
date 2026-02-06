<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Shutdown handler to catch fatal errors (parse/compile) and display a friendly error page
register_shutdown_function(function () {
    $err = error_get_last();
    if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
        http_response_code(500);
        $message = isset($err['message']) ? htmlspecialchars($err['message']) : 'Fatal error';
        $file = isset($err['file']) ? htmlspecialchars($err['file']) : '';
        $line = isset($err['line']) ? (int) $err['line'] : '';

        // Minimal HTML matching the app style
        echo "<!doctype html><html><head><meta charset=\"utf-8\"><meta name=\"viewport\" content=\"width=device-width,initial-scale=1\"><title>Server Error</title><style>
        body{font-family: -apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#fff;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0}
        .card{background:#fff;color:#222;padding:30px;border-radius:12px;max-width:800px;box-shadow:0 20px 60px rgba(0,0,0,0.2)}
        .code{font-weight:700;color:#667eea}
        .trace{margin-top:12px;font-family:monospace;background:#f5f5f5;padding:10px;border-radius:6px;color:#333;overflow:auto}
        a.btn{display:inline-block;margin-top:12px;padding:8px 14px;border-radius:6px;background:#667eea;color:#fff;text-decoration:none}
        </style></head><body><div class=\"card\"><h1>Server Error</h1><p><strong>Error:</strong> <span class=\"code\">{$message}</span></p>";

        if ($file) {
            echo "<p><strong>Location:</strong> {$file}:{$line}</p>";
        }

        // If possible, include a small dump of backtrace (limited)
        if (function_exists('debug_backtrace')) {
            $bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            $lines = [];
            $max = 5;
            foreach ($bt as $i => $b) {
                if ($i === 0) continue;
                $f = isset($b['file']) ? $b['file'] : '';
                $l = isset($b['line']) ? $b['line'] : '';
                $fn = isset($b['function']) ? $b['function'] : '';
                $lines[] = htmlspecialchars("{$f}:{$l} -> {$fn}");
                if (count($lines) >= $max) break;
            }
            if ($lines) {
                echo "<div class=\"trace\"><strong>Trace (partial):</strong><br>" . implode('<br>', $lines) . "</div>";
            }
        }

        echo "<a class=\"btn\" href=\"/\">Return Home</a>";
        echo "</div></body></html>";
        exit(1);
    }
});

// Force debug off and disable PHP error display so custom error pages are used
putenv('APP_DEBUG=false');
putenv('APP_ENV=production');
$_ENV['APP_DEBUG'] = 'false';
$_ENV['APP_ENV'] = 'production';
ini_set('display_errors', '0');
error_reporting(0);

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
