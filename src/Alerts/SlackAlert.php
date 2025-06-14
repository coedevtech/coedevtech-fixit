<?php 

namespace Fixit\Alerts;

use Fixit\Contracts\FixitAlertInterface;
use Illuminate\Support\Facades\Http;
use Throwable;

class SlackAlert implements FixitAlertInterface
{
    public function send(string $message, ?Throwable $exception = null, ?string $suggestion = null): void
    {
        $webhook = config('fixit.notifications.slack_webhook');

        if (!$webhook) {
            return;
        }

        $blocks = [
            [
                'type' => 'section',
                'text' => [
                    'type' => 'mrkdwn',
                    'text' => "*🚨 An exception occurred:* \n```{$message}```",
                ],
            ],
        ];

        if ($suggestion) {
            $blocks[] = [
                'type' => 'section',
                'text' => [
                    'type' => 'mrkdwn',
                    'text' => "*🧠 AI Suggestion:*\n```{$suggestion}```",
                ],
            ];
        }

        Http::post($webhook, [
            'text' => 'FixIt Exception Alert',
            'blocks' => $blocks,
        ]);
    }
}
