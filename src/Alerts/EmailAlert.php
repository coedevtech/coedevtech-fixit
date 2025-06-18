<?php

namespace Fixit\Alerts;

use Fixit\Contracts\FixitAlertInterface;
use Fixit\Mail\ErrorOccurredNotification;
use Fixit\Support\NotificationEmailResolver;
use Illuminate\Support\Facades\Mail;

class EmailAlert implements FixitAlertInterface
{
    /**
     * Send an error alert via email.
     *
     * @param string         $message     Summary of the error
     * @param string|null    $exception   Short trace or file/line info
     * @param string|null    $suggestion  AI-generated suggestion (optional)
     * @param int|null       $occurrences Number of times this error occurred
     * @param string|null    $date        Last seen timestamp
     * @param string|null    $environment Application environment (e.g., production)
     */
    public function send(
        string $message,
        ?string $exception = null,
        ?string $suggestion = null,
        ?int $occurrences = null,
        ?string $date = null,
        ?string $environment = null
    ): void {
        $mailable = new ErrorOccurredNotification(
            messageContent: $message,
            exception: $exception,
            suggestion: $suggestion,
            occurrences: $occurrences,
            date: $date ?? now()->toDateTimeString(),
            environment: $environment ?? app()->environment()
        );

        $recipients = NotificationEmailResolver::resolve();

        if (empty($recipients)) {
            logger()->warning('Fixit: No valid email recipients found for error alert.');
            return;
        }

        foreach ($recipients as $recipient) {
            try {
                if ($this->shouldQueue()) {
                    Mail::to($recipient)->queue($mailable);
                } else {
                    Mail::to($recipient)->send($mailable);
                }
            } catch (\Throwable $e) {
                logger()->warning('Fixit email alert failed', [
                    'email' => $recipient,
                    'reason' => $e->getMessage(),
                ]);
            }
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
