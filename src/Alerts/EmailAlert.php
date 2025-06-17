<?php

namespace Fixit\Alerts;

use Fixit\Contracts\FixitAlertInterface;
use Fixit\Mail\ErrorOccurredNotification;
use Illuminate\Support\Facades\Mail;

class EmailAlert implements FixitAlertInterface
{
    /**
     * Send an error alert via email.
     *
     * @param string         $message     Summary of the error
     * @param string|null    $exception   Optional exception object (not used here)
     * @param string|null    $suggestion  Optional AI-generated suggestion
     * @param int|null       $occurrences Optional
     * @param string|null    $date        Optional
     * @param string|null    $environment Optional
     */
    public function send(
        string $message,
        ?string $exception = null,
        ?string $suggestion = null,
        ?int $occurrences = null,
        ?string $date = null,
        ?string $environment = null
    ): void
    {
        // Send the error notification email to the configured recipient
        $mailable = new ErrorOccurredNotification(
            messageContent: $message,
            exception: $exception,
            suggestion: $suggestion,
            occurrences: $occurrences,
            date: $date ?? now()->toDateTimeString(),
            environment: $environment ?? app()->environment()
        );

        $recipient = config('fixit.notifications.email');

        try {
            if ($this->shouldQueue()) {
                Mail::to($recipient)->queue($mailable);
            } else {
                Mail::to($recipient)->send($mailable);
            }
        } catch (\Throwable $e) {
            logger()->warning('Fixit email alert failed', ['reason' => $e->getMessage()]);
        }
    }

    /**
     * Determine if the mailable should be queued based on the queue driver.
     */
    protected function shouldQueue(): bool
    {
        $driver = config('queue.default');
        return !in_array(strtolower($driver), ['sync', 'null', 'array'], true);
    }
}
