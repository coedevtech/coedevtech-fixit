<?php 

namespace Fixit\Alerts;

use Fixit\Contracts\FixitAlertInterface;
use Illuminate\Support\Facades\Http;

class SlackAlert implements FixitAlertInterface
{
    /**
     * Send an exception alert to a configured Slack webhook.
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
        // Retrieve Slack webhook URL from config
        $webhook = config('fixit.notifications.slack_webhook');

        // Abort if no webhook is set
        if (!$webhook) {
            return;
        }

        // Build Slack message blocks
        $blocks = [
            [
                'type' => 'section',
                'text' => [
                    'type' => 'mrkdwn',
                    'text' => "*🚨 An exception occurred:* \n```{$message}```",
                ],
            ],
        ];

        // Optionally include AI suggestion block
        if ($suggestion) {
            $blocks[] = [
                'type' => 'section',
                'text' => [
                    'type' => 'mrkdwn',
                    'text' => "*🧠 AI Suggestion:*\n```{$suggestion}```",
                ],
            ];
        }

        // Send the formatted payload to Slack
        Http::post($webhook, [
            'text' => 'FixIt Exception Alert',
            'blocks' => $blocks,
        ]);
    }
}
