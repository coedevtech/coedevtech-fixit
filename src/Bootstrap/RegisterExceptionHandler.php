<?php

namespace Fixit\Bootstrap;

use Illuminate\Contracts\Debug\ExceptionHandler;

class RegisterExceptionHandler
{
    public static function handle($app): void
    {
        $handler = $app->make(ExceptionHandler::class);

        if (method_exists($handler, 'reportable')) {
            $handler->reportable(function (\Throwable $e) {
                app(\Fixit\Listeners\LogExceptionToDb::class)->handle($e);
            });
        }
    }
}

