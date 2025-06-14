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

    /*
    |--------------------------------------------------------------------------
    | Automatic Fix Status
    |--------------------------------------------------------------------------
    | Automatically marks old errors as "fixed" if they haven't reoccurred
    | in a defined number of days. This helps keep your error log clean
    | by closing stale issues.
    |
    | - `enabled`: Turns the feature on/off
    | - `check_interval_minutes`: How often the check should run (in minutes)
    | - `inactivity_days_to_fix`: How many days without reoccurrence before marking as fixed
    */
    'auto_fix' => [
        'enabled' => true,
        'check_interval_minutes' => 2,
        'inactivity_days_to_fix' => 2,
    ],

    /*
    |--------------------------------------------------------------------------
    | AI-Powered Suggestions (Optional)
    |--------------------------------------------------------------------------
    | Users can enable AI-generated suggestions for fixing errors. To use this,
    | they must provide a proxy endpoint or their own OpenAI credentials.
    */
    'ai' => [
        'enabled' => env('FIXIT_AI_ENABLED', false), // default: off
        'provider' => env('FIXIT_AI_PROVIDER', 'openai'), // or 'fixit-proxy'
        'api_url' => env('FIXIT_AI_API_URL', null), // for proxy
        'api_key' => env('FIXIT_AI_API_KEY', null), // for direct OpenAI use
        'timeout' => env('FIXIT_AI_TIMEOUT', 10),
    ],
];
