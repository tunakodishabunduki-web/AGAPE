<?php

return [
    // Nothing in this app currently dispatches a queued job — image compression
    // and TOTP checks all happen synchronously — so 'sync' is deliberate, not
    // a placeholder. Switch to 'database' here (table already exists via
    // Laravel's defaults) if you later add something worth queuing.
    'default' => env('QUEUE_CONNECTION', 'sync'),

    'connections' => [
        'sync' => ['driver' => 'sync'],
        'database' => [
            'driver' => 'database',
            'table' => 'jobs',
            'queue' => 'default',
            'retry_after' => 90,
        ],
    ],

    'batching' => ['database' => 'mysql', 'table' => 'job_batches'],
    'failed' => ['driver' => env('QUEUE_FAILED_DRIVER', 'database-uuids'), 'database' => 'mysql', 'table' => 'failed_jobs'],
];
