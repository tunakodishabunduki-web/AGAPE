<?php

return [
    // 'web' is what Auth::login($user) in AuthController writes to and what
    // Sanctum's SPA cookie mode checks against — there is no separate API
    // guard because Sanctum handles that translation for same-domain requests.
    'defaults' => [
        'guard' => 'web',
        'passwords' => 'staff_users',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'staff_users',
        ],
    ],

    // Named 'staff_users' (not the Laravel-default 'users') everywhere in this
    // file to match the actual model — StaffUser, not a default User model
    // that doesn't exist in this project at all.
    'providers' => [
        'staff_users' => [
            'driver' => 'eloquent',
            'model' => App\Models\StaffUser::class,
        ],
    ],

    'passwords' => [
        'staff_users' => [
            'provider' => 'staff_users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,
];
