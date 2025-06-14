<?php

namespace Fixit\Alerts;

use Fixit\Contracts\FixitAlertInterface;
use Fixit\Mail\ErrorOccurredNotification;
use Illuminate\Support\Facades\Mail;
use Throwable;

class EmailAlert implements FixitAlertInterface
{
    public function send(string $message, ?Throwable $exception = null, ?string $suggestion = null): void
    {
        Mail::to(config('fixit.notifications.email'))
            ->send(new ErrorOccurredNotification($message, $suggestion));
    }
}


