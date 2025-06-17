<?php

namespace Fixit\Alerts;

use Fixit\Contracts\FixitAlertInterface;
use Fixit\Mail\ErrorOccurredNotification;
use Illuminate\Support\Facades\Mail;
use Throwable;

class EmailAlert implements FixitAlertInterface
{
    /**
     * Send an error alert via email.
     *
     * @param string         $message     Summary of the error
     * @param Throwable|null $exception   Optional exception object (not used here)
     * @param string|null    $suggestion  Optional AI-generated suggestion
     * @param int|null       $occurrences Optional
     * @param string|null    $date        Optional
     * @param string|null    $environment Optional
     */
    public function send(
        string $message,
        ?Throwable $exception = null,
        ?string $suggestion = null,
        ?int $occurrences = null,
        ?string $date = null,
        ?string $environment = null
    ): void
    {
        // Send the error notification email to the configured recipient
        Mail::to(config('fixit.notifications.email'))
            ->send(new ErrorOccurredNotification(
                $message,
                $exception,
                $suggestion,
                $occurrences,
                $date,
                $environment
            ));
    }
}
