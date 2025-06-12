<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Fixit\Listeners\LogExceptionToDb;
use Fixit\Tests\TestCase;

it('logs an error to the database', function () {
    // Enable minimal config
    Config::set('fixit.notifications.send_on_error', false);
    Config::set('fixit.encryption.enabled', false);

    // ✅ Create a real request
    $request = Request::create('/test-url', 'GET', ['foo' => 'bar']);
    $request->setUserResolver(fn () => null); // Prevent auth-related error
    $request->server->set('REMOTE_ADDR', '127.0.0.1');

    // Bind to the container
    app()->instance('request', $request);

    // Trigger logger
    $logger = app(LogExceptionToDb::class);
    $logger->handle(new Exception('Test exception'));

    // ✅ Assert DB log exists
    $entry = \Fixit\Models\FixitError::latest()->first();

    expect($entry)->not->toBeNull();
    expect($entry->url)->toContain('/test-url');
    expect($entry->status)->toBe('not_fixed');
})->uses(TestCase::class);
