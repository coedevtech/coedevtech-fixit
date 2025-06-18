<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Encryption Settings
    |--------------------------------------------------------------------------
    */
    'encryption' => [
        'enabled' => env('FIXIT_ENCRYPTION_ENABLED', false),
        'key' => env('FIXIT_ENCRYPTION_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Notification Settings
    |--------------------------------------------------------------------------
    | Available drivers:
    | - email
    | - slack
    */
    'notifications' => [
        'driver' => env('FIXIT_NOTIFICATION_DRIVER', 'email'),
        'send_on_error' => env('FIXIT_SEND_EMAIL', false),
        'email' => env('FIXIT_NOTIFICATION_EMAIL', 'email@example.com'),
        'allow_multiple' => env('FIXIT_ALLOW_MULTIPLE_EMAILS', false),
        'slack_webhook' => '',
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
        'enabled' => env('FIXIT_AUTO_FIX_ENABLED', true),
        'check_interval_minutes' => env('FIXIT_AUTO_FIX_CHECK_INTERVAL_MINUTES', 2),
        'inactivity_days_to_fix' => env('FIXIT_AUTO_FIX_INACTIVITY_DAYS_TO_FIX', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | AI-Powered Suggestions (Multi-provider Support)
    |--------------------------------------------------------------------------
    | Available providers:
    | - openai:      Uses OpenAI API (e.g. gpt-3.5-turbo, gpt-4)
    | - groq:        Uses Groq’s ultra-fast LLM API (e.g. mixtral-8x7b, llama3-70b)
    | - together:    Uses Together.ai’s hosted open models
    | - fixit-proxy: Custom internal proxy endpoint
    */
    'ai' => [
        'enabled' => env('FIXIT_AI_ENABLED', false),
        'provider' => env('FIXIT_AI_PROVIDER', 'openai'), // openai | groq | together | mistral | fixit-proxy
        'api_url' => env('FIXIT_AI_API_URL', null), // Used for fixit-proxy
        'api_key' => env('FIXIT_AI_API_KEY', null), // Used for OpenAI, Groq, TogetherAI, Mistral
        'model' => env('FIXIT_AI_MODEL', null), // Optional: Custom model override per provider e.g., gpt-4, mixtral-8x7b-32768, mistral-small
        'timeout' => env('FIXIT_AI_TIMEOUT', 10),
    ],
];
