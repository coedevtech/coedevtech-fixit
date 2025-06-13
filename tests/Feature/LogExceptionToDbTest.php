<?php

use Fixit\Listeners\LogExceptionToDb;
use Fixit\Models\FixitError;
use Fixit\Contracts\FixitAlertInterface;
use Fixit\Facades\Fixit;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    // Set up encryption config
    Config::set('fixit.encryption.enabled', true);
    Config::set('fixit.notifications.send_on_error', true);
    $key = base64_encode(random_bytes(32));
    putenv("FIXIT_ENCRYPTION_KEY={$key}");

    // Create a fake fixit_errors table for testing
    Schema::dropIfExists('fixit_errors');
    Schema::create('fixit_errors', function ($table) {
        $table->id();
        $table->text('url')->nullable();
        $table->json('request')->nullable();
        $table->json('response')->nullable();
        $table->string('ip')->nullable();
        $table->string('status')->default('not_fixed');
        $table->string('exception')->nullable();
        $table->text('file')->nullable();
        $table->integer('line')->nullable();
        $table->longText('trace')->nullable();
        $table->timestamps();
    });
});

it('logs, encrypts, and correctly decrypts an exception', function () {
    // Mock FixitAlertInterface to prevent real notifications
    $mockNotifier = Mockery::mock(FixitAlertInterface::class);
    $mockNotifier->shouldReceive('send')->once();

    // Simulate a throwable
    $exception = new RuntimeException('Something exploded!');

    // Log it
    $listener = new LogExceptionToDb($mockNotifier);
    $listener->handle($exception);

    // Fetch latest logged error
    $log = FixitError::latest()->first();

    expect($log)->not()->toBeNull();

    // Decrypt values and verify contents
    $decryptedResponse = Fixit::decrypt($log->response);
    $decryptedException = Fixit::decrypt($log->exception);
    $decryptedTrace = Fixit::decrypt($log->trace);

    expect($decryptedResponse['message'])->toBe('Something exploded!');
    expect($decryptedException)->toBe(RuntimeException::class);
    expect($decryptedTrace)->toBeString()->not()->toBe('');
});
