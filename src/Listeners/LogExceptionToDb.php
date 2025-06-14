<?php

namespace Fixit\Listeners;

use Throwable;
use Fixit\Facades\Fixit;
use Fixit\Models\FixitError;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Fixit\Contracts\FixitAlertInterface;

class LogExceptionToDb
{
    public function __construct(
        protected FixitAlertInterface $notifier
    ) {}

    public function handle(Throwable $e): void
    {
        try {
            $data = [
                'url'       => Request::fullUrl(),
                'request'   => Request::all(),
                'response'  => ['message' => $e->getMessage()],
                'ip'        => Request::ip(),
                'status'    => 'not_fixed',
                'exception' => get_class($e),
                'file'      => $e->getFile(),
                'line'      => $e->getLine(),
                'trace'     => $e->getTraceAsString(),
            ];

            if (Config::get('fixit.encryption.enabled') && env('FIXIT_ENCRYPTION_KEY')) {
                if (isset($data['request'])) {
                    $data['request'] = Fixit::encrypt($data['request']);
                }
                if (isset($data['response'])) {
                    $data['response'] = Fixit::encrypt($data['response']);
                }
            }

            FixitError::create($data);

            if (Config::get('fixit.notifications.send_on_error')) {
                $this->notifier->send($e->getMessage(), $e);
            }

        } catch (Throwable $fail) {
            $this->notifier->send("FixIt failed to log exception: {$fail->getMessage()}", $fail);
        }
    }
}
