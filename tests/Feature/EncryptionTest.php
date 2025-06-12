<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Fixit\Listeners\LogExceptionToDb;
use Fixit\Tests\TestCase;

it('encrypts data when enabled', function () {
    // Set encryption config
    Config::set('fixit.encryption.enabled', true);
    Config::set('fixit.encryption.key', base64_encode(random_bytes(32)));

    // ✅ Create a real Laravel Request instance
    $request = Request::create('/encrypted-url', 'GET', ['name' => 'Encrypted Test']);
    $request->setUserResolver(fn () => null); // prevents Laravel from throwing auth-related errors
    $request->server->set('REMOTE_ADDR', '127.0.0.1');

    // Bind to container
    app()->instance('request', $request);

    // Run the logger
    $logger = app(LogExceptionToDb::class);
    $logger->handle(new Exception('Sensitive error'));

    // Assert encrypted result in DB
    $record = \Fixit\Models\FixitError::latest()->first();

    expect($record->url)->not->toBe('/encrypted-url'); // it's encrypted
    expect($record->status)->toBe('not_fixed');
})->uses(TestCase::class);
