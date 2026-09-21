<?php

return [
    // Nothing in this app sends email yet (staff invites/resets show the
    // one-time password directly in the dashboard instead of emailing it).
    // 'log' is a safe default that writes would-be emails to storage/logs
    // instead of silently failing or accidentally sending real mail from a
    // half-configured SMTP block.
    'default' => env('MAIL_MAILER', 'log'),

    'mailers' => [
        'smtp' => [
            'transport' => 'smtp',
            'host' => env('MAIL_HOST', '127.0.0.1'),
            'port' => env('MAIL_PORT', 587),
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
        ],
        'log' => ['transport' => 'log', 'channel' => env('MAIL_LOG_CHANNEL')],
    ],

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'no-reply@agapefamilyfoundation.org'),
        'name' => env('MAIL_FROM_NAME', 'Agape Family Foundation'),
    ],
];
