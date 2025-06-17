<?php

use Fixit\Contracts\FixitAlertInterface;
use Fixit\Facades\Fixit;
use Fixit\Models\FixitError;
use Fixit\Listeners\LogExceptionToDb;
use Fixit\Support\AiFixSuggester;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    Config::set('fixit.encryption.enabled', true);
    Config::set('fixit.notifications.send_on_error', true);
    Config::set('fixit.ai.enabled', false); // turn off AI logic for this test

    $key = base64_encode(random_bytes(32));
    putenv("FIXIT_ENCRYPTION_KEY={$key}");

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
        $table->string('environment')->nullable();
        $table->string('fingerprint')->nullable();
        $table->dateTime('last_seen_at')->nullable();
        $table->timestamps();
    });
});

it('logs, encrypts, and correctly decrypts an exception', function () {
    // Mock FixitAlertInterface
    $mockNotifier = Mockery::mock(FixitAlertInterface::class);
    $mockNotifier->shouldReceive('send')->once();

    // Mock AiFixSuggester (not used because ai.enabled is false)
    $mockSuggester = Mockery::mock(AiFixSuggester::class);

    // Create and handle a throwable
    $exception = new RuntimeException('Something exploded!');
    $listener = new LogExceptionToDb($mockNotifier, $mockSuggester);
    $listener->handle($exception);

    $log = FixitError::latest()->first();

    expect($log)->not()->toBeNull();

    $decryptedResponse = Fixit::decrypt($log->response);
    $decryptedException = Fixit::decrypt($log->exception);
    $decryptedTrace = Fixit::decrypt($log->trace);

    expect($decryptedResponse['message'])->toBe('Something exploded!');
    expect($decryptedException)->toBe(RuntimeException::class);
    expect($decryptedTrace)->toBeString()->not()->toBe('');
});

