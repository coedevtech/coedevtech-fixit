<?php

use Fixit\Alerts\EmailAlert;
use Fixit\Contracts\FixitAlertInterface;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;
use Fixit\Listeners\LogExceptionToDb;
use Fixit\Mail\ErrorOccurredNotification;
use Fixit\Tests\TestCase;

it('sends email on error when enabled', function () {
    // Fake mail
    Mail::fake();

    app()->bind(Fixit\Contracts\FixitAlertInterface::class, fn () => new Fixit\Alerts\EmailAlert());

    // Enable email in config
    Config::set('fixit.notifications.send_on_error', true);
    Config::set('fixit.notifications.email', 'test@example.com');

    // Create a real Request instance (not mocked)
    $request = Request::create('/test-url', 'GET', ['foo' => 'bar']);
    $request->setLaravelSession(app('session.store')); // optional if session is used
    $request->setUserResolver(fn () => null); // prevent user resolver crash
    $request->server->set('REMOTE_ADDR', '127.0.0.1');

    // Manually bind to Laravel container so Request::ip() etc. work
    app()->instance('request', $request);

    // Fire the exception logger
    $logger = app(LogExceptionToDb::class);
    $logger->handle(new Exception('Test email'));

    Mail::assertSent(ErrorOccurredNotification::class, function ($mail) {
        return str_contains($mail->messageContent, 'Test email');
    });
})->uses(TestCase::class);


