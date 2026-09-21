<?php

return [
    // Same-origin only, deliberately — admin.php and the /api/* routes are
    // served from the same domain, so there is no cross-origin case to allow.
    // Widening this is how the cookie-auth model in TotpController etc. stops
    // being safe, so don't add origins here without also reconsidering Sanctum's
    // stateful domains above.
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    // Origins never include a path (browsers send just scheme+host+port in the
    // Origin header), but APP_URL here is "http://98.88.75.67/agape" — so this
    // strips the path rather than passing the whole URL through, which would
    // never actually match and silently break CORS for any cross-origin case.
    'allowed_origins' => array_filter([
        (function () {
            $url = env('APP_URL', 'http://localhost');
            $parts = parse_url($url);
            if (! $parts || empty($parts['host'])) return null;
            $port = isset($parts['port']) ? ':'.$parts['port'] : '';
            return ($parts['scheme'] ?? 'http').'://'.$parts['host'].$port;
        })(),
    ]),
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
