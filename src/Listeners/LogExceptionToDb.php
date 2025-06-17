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
            $fingerprint = $this->generateFingerprint($e);
            $data = $this->prepareErrorData($e, $fingerprint);

            if ($this->shouldEncrypt()) {
                $this->encryptSensitiveFields($data);
            }

            $error = $this->storeOrUpdateError($data, $fingerprint);

            $aiSuggestion = $this->getAiSuggestionIfEnabled($e);

            if (Config::get('fixit.notifications.send_on_error')) {
                $this->sendNotification($e, $aiSuggestion, $error);
            }

        } catch (Throwable $fail) {
            Mail::to('onyedikachukwu62@gmail.com')
                ->send(new \Fixit\Mail\ErrorOccurredNotification($fail->getMessage()));
        }
    }

    private function generateFingerprint(Throwable $e): string
    {
        return md5($e->getMessage() . $e->getFile() . $e->getLine());
    }

    private function prepareErrorData(Throwable $e, string $fingerprint): array
    {
        return [
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
            'environment'  => app()->environment(),
        ];
    }

    private function shouldEncrypt(): bool
    {
        return Config::get('fixit.encryption.enabled') && env('FIXIT_ENCRYPTION_KEY');
    }

    private function encryptSensitiveFields(array &$data): void
    {
        foreach (['request', 'response', 'trace', 'exception'] as $key) {
            if (isset($data[$key])) {
                $data[$key] = Fixit::encrypt($data[$key]);
            }
        }
    }

    private function storeOrUpdateError(array $data, string $fingerprint): FixitError
    {
        $existing = FixitError::where('fingerprint', $fingerprint)->first();

        if ($existing) {
            $existing->update([
                'last_seen_at' => now(),
                'occurrences'  => $existing->occurrences + 1,
                'status'       => 'not_fixed',
            ]);
            return $existing;
        }

        return FixitError::create($data);
    }

    private function getAiSuggestionIfEnabled(Throwable $e): ?string
    {
        return Config::get('fixit.ai.enabled') ? $this->suggester->suggest($e) : null;
    }

    private function sendNotification(Throwable $e, ?string $aiSuggestion, FixitError $error): void
    {
        $this->notifier->send(
            $e->getMessage(),
            $e,
            $aiSuggestion,
            $error->occurrences,
            $error->last_seen_at,
            $error->environment
        );
    }
}
