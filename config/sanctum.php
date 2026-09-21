<?php

use Laravel\Sanctum\Sanctum;

return [
    // Comma-separated in .env (SANCTUM_STATEFUL_DOMAINS) — these are the ONLY
    // origins allowed to authenticate via cookie instead of a bearer token.
    // admin.php is served from the same domain, so it just works via cookies;
    // nothing else needs a token at all.
    'stateful' => explode(',', (string) env(
        'SANCTUM_STATEFUL_DOMAINS',
        'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1'
    )),

    'guard' => ['web'],
    'expiration' => null,
    'token_prefix' => env('SANCTUM_TOKEN_PREFIX', ''),

    'middleware' => [
        'authenticate_session' => Laravel\Sanctum\Http\Middleware\AuthenticateSession::class,
        'encrypt_cookies' => Illuminate\Cookie\Middleware\EncryptCookies::class,
        'validate_csrf_token' => Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
    ],
];
