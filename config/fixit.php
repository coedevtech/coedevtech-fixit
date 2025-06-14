<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Encryption Settings
    |--------------------------------------------------------------------------
    */
    'encryption' => [
        'enabled' => env('FIXIT_ENCRYPTION', false),
        'key' => env('FIXIT_ENCRYPTION_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Notification Settings
    |--------------------------------------------------------------------------
    */
    'notifications' => [
        'driver' => env('FIXIT_NOTIFICATION_DRIVER', 'email'),
        'send_on_error' => env('FIXIT_SEND_EMAIL', false),
        'email' => env('FIXIT_NOTIFICATION_EMAIL', 'admin@example.com'),
        'slack_webhook' => "",
    ],

    /*
    |--------------------------------------------------------------------------
    | Error Logging Settings
    |--------------------------------------------------------------------------
    */
    'logging' => [
        'table' => 'fixit_errors',
        'status_default' => 'not_fixed',
    ],

    /*
    |--------------------------------------------------------------------------
    | Future Scalability
    |--------------------------------------------------------------------------
    | Use these fields for upcoming features like retention, log channels, etc.
    */
    'retention' => [
        'enabled' => false,
        'days' => 30,
    ],

    'auto_fix' => [
        'enabled' => true,
        'check_interval_minutes' => 2,
        'inactivity_days_to_fix' => 2,
    ],
];
