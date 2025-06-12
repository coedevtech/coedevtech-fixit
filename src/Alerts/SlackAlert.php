<?php 

namespace Fixit\Alerts;

use Fixit\Contracts\FixitAlertInterface;
use Illuminate\Support\Facades\Http;

class SlackAlert implements FixitAlertInterface
{
    public function send(string $message): void
    {
        $webhook = config('fixit.notifications.slack_webhook');
        if ($webhook) {
            Http::post($webhook, [
                'text' => $message,
            ]);
        }
    }
}


