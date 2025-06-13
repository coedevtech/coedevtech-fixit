<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Fixit\Listeners\LogExceptionToDb;

class Handler extends ExceptionHandler
{
    public function report(Throwable $exception)
    {
        try {
            app(LogExceptionToDb::class)->handle($exception);
        } catch (\Throwable $fail) {
            // fallback to Laravel logger if FixIt fails
            logger()->error('FixIt failed: ' . $fail->getMessage());
        }

        parent::report($exception);
    }
}
