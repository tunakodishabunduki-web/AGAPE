<?php

return [
    // 'database' matches SESSION_DRIVER in .env.example — Sanctum's SPA cookie
    // auth needs sessions to actually persist somewhere, and 'database' avoids
    // needing Redis just for this.
    'driver' => env('SESSION_DRIVER', 'database'),
    'lifetime' => (int) env('SESSION_LIFETIME', 120),
    'expire_on_close' => false,
    'encrypt' => false,
    'files' => storage_path('framework/sessions'),
    'connection' => env('SESSION_CONNECTION'),
    'table' => 'sessions',
    'store' => env('SESSION_STORE'),
    'lottery' => [2, 100],
    'cookie' => env('SESSION_COOKIE', 'agape_session'),
    'path' => '/',
    'domain' => env('SESSION_DOMAIN'),
    // 'lax' is what makes the admin dashboard's fetch() calls carry the auth
    // cookie correctly while still protecting against basic CSRF vectors.
    'secure' => env('SESSION_SECURE_COOKIE', true),
    'http_only' => true,
    'same_site' => 'lax',
    'partitioned' => false,
];
