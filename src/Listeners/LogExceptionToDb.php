<?php

namespace Fixit\Listeners;

use Throwable;
use Fixit\Facades\Fixit;
use Fixit\Models\FixitError;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Fixit\Contracts\FixitAlertInterface;
use Fixit\Support\AiFixSuggester;
use Illuminate\Support\Facades\Mail;

class LogExceptionToDb
{
    /**
     * Inject the alerting and AI suggestion services.
     */
    public function __construct(
        protected FixitAlertInterface $notifier,
        protected AiFixSuggester $suggester
    ) {}

    /**
     * Handle and log the exception to the Fixit database table.
     * Optionally encrypt sensitive data and send notifications.
     */
    public function handle(Throwable $e): void
    {
        try {
            // Generate a unique fingerprint for the error based on message, file, and line
            $fingerprint = md5($e->getMessage() . $e->getFile() . $e->getLine());

            // Prepare data for storage
            $data = [
                'url'          => Request::fullUrl(),
                'request'      => Request::all(),
                'response'     => ['message' => $e->getMessage()],
                'ip'           => Request::ip(),
                'exception'    => get_class($e),
                'file'         => $e->getFile(),
                'line'         => $e->getLine(),
                'trace'        => $e->getTraceAsString(),
                'status'       => 'not_fixed',
                'fingerprint'  => $fingerprint,
                'last_seen_at' => now(),
            ];

            // Encrypt request and response data if enabled
            if (Config::get('fixit.encryption.enabled') && env('FIXIT_ENCRYPTION_KEY')) {
                foreach (['request', 'response', 'trace', 'exception'] as $key) {
                    if (isset($data[$key])) {
                        $data[$key] = Fixit::encrypt($data[$key]);
                    }
                }
            }

            // Check if this exception already exists in the database
            $existing = FixitError::where('fingerprint', $fingerprint)->first();

            if ($existing) {
                // If already logged, update last seen time and increment occurrence count
                $existing->update([
                    'last_seen_at' => now(),
                    'occurrences'  => $existing->occurrences + 1,
                    'status'       => 'not_fixed',
                ]);
            } else {
                // Otherwise, create a new error entry
                FixitError::create($data);
            }

            // Optionally get an AI-generated suggestion for the error
            $aiSuggestion = null;
            if (Config::get('fixit.ai.enabled')) {
                $aiSuggestion = $this->suggester->suggest($e);
            }

            // Send notification if enabled
            if (Config::get('fixit.notifications.send_on_error')) {
                $this->notifier->send($e->getMessage(), $e, $aiSuggestion);
            }

        } catch (Throwable $fail) {
            // If something fails during logging, send a fallback alert to the author
            Mail::to('onyedikachukwu62@gmail.com')
                ->send(new \Fixit\Mail\ErrorOccurredNotification($fail->getMessage()));
        }
    }
}
