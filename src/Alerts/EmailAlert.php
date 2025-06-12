<?php

namespace Fixit\Alerts;

use Fixit\Contracts\FixitAlertInterface;
use Fixit\Mail\ErrorOccurredNotification;
use Illuminate\Support\Facades\Mail;

class EmailAlert implements FixitAlertInterface
{
    public function send(string $message): void
    {
        Mail::to(config('fixit.notifications.email'))
            ->send(new ErrorOccurredNotification($message));
    }
}


