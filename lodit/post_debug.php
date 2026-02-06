<?php
$url = 'http://127.0.0.1:8000/api/debug/update-profile';
$data = [
    'session_id' => 1,
    'name' => 'Y From Debug',
    'email' => 'y.debug@example.com',
    'phone' => '555-0000'
];
$options = [
    'http' => [
        'header'  => "Content-type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($data),
        'ignore_errors' => true,
    ],
];
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
echo "HTTP response:\n" . ($result ?: 'NO BODY') . "\n";
print_r($http_response_header);
