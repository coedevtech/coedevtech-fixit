<?php

namespace Fixit\Listeners;

use Throwable;
use Fixit\Facades\Fixit;
use Fixit\Models\FixitError;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Fixit\Contracts\FixitAlertInterface;
use Fixit\Support\AiFixSuggester;

class LogExceptionToDb
{
    public function __construct(
        protected FixitAlertInterface $notifier,
        protected AiFixSuggester $suggester
    ) {}

    public function handle(Throwable $e): void
    {
        try {
            $fingerprint = md5($e->getMessage() . $e->getFile() . $e->getLine());

            $data = [
                'url'         => Request::fullUrl(),
                'request'     => Request::all(),
                'response'    => ['message' => $e->getMessage()],
                'ip'          => Request::ip(),
                'exception'   => get_class($e),
                'file'        => $e->getFile(),
                'line'        => $e->getLine(),
                'trace'       => $e->getTraceAsString(),
                'status'      => 'not_fixed',
                'fingerprint' => $fingerprint,
                'last_seen_at'=> now(),
            ];

            if (Config::get('fixit.encryption.enabled') && env('FIXIT_ENCRYPTION_KEY')) {
                if (isset($data['request'])) {
                    $data['request'] = Fixit::encrypt($data['request']);
                }
                
                if (isset($data['response'])) {
                    $data['response'] = Fixit::encrypt($data['response']);
                }
            }

            $existing = FixitError::where('fingerprint', $fingerprint)->first();

            if ($existing) {
                $existing->update([
                    'last_seen_at' => now(),
                    'occurrences'  => $existing->occurrences + 1,
                    'status'       => 'not_fixed',
                ]);
            } else {
                FixitError::create($data);
            }

            $aiSuggestion = null;
            if (Config::get('fixit.ai.enabled')) {
                $aiSuggestion = $this->suggester->suggest($e);
            }

            if (Config::get('fixit.notifications.send_on_error')) {
                $this->notifier->send($e->getMessage(), $e, $aiSuggestion);
            }

        } catch (Throwable $fail) {
            $this->notifier->send("FixIt failed to log exception: {$fail->getMessage()}", $fail);
        }
    }

}
