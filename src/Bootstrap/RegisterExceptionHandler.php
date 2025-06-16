<?php

namespace Fixit\Bootstrap;

use Illuminate\Contracts\Debug\ExceptionHandler;

class RegisterExceptionHandler
{
    /**
     * Registers a reportable callback to log exceptions into the Fixit system.
     *
     * This uses Laravel's reportable() method on the exception handler
     * to hook into the reporting process without overriding the default handler.
     */
    public static function handle($app): void
    {
        // Resolve Laravel's exception handler from the container
        $handler = $app->make(ExceptionHandler::class);

        // Ensure the handler supports the `reportable` method (available in Laravel 7+)
        if (method_exists($handler, 'reportable')) {
            // Attach a reportable closure to log exceptions to Fixit
            $handler->reportable(function (\Throwable $e) {
                app(\Fixit\Listeners\LogExceptionToDb::class)->handle($e);
            });
        }
    }
}
