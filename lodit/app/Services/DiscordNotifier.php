<?php

namespace App\Services;

class DiscordNotifier
{
    /**
     * Send a simple message to the configured Discord webhook.
     * Reads webhook from env('DISCORD_WEBHOOK_URL') by default.
     */
    public static function notify(string $message, string $webhook = null)
    {
        $url = $webhook ?? env('DISCORD_WEBHOOK_URL');
        if (!$url) return false;

        $payload = json_encode(['content' => $message]);

        // Use curl if available
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            $resp = curl_exec($ch);
            $err = curl_errno($ch);
            curl_close($ch);
            return ($err === 0);
        }

        // Fallback to file_get_contents
        $opts = [
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n",
                'content' => $payload,
                'timeout' => 5,
            ]
        ];

        $context = stream_context_create($opts);
        $result = @file_get_contents($url, false, $context);
        return $result !== false;
    }
}
