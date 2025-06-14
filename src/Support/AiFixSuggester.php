<?php

namespace Fixit\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Throwable;

class AiFixSuggester
{
    public function suggest(Throwable $exception): ?string
    {
        if (!Config::get('fixit.ai.enabled')) {
            return null;
        }

        $prompt = $this->buildPrompt($exception);

        $provider = Config::get('fixit.ai.provider', 'openai');

        return match ($provider) {
            'fixit-proxy' => $this->callProxy($prompt),
            'openai' => $this->callOpenAiDirectly($prompt),
            default => null,
        };
    }

    protected function callProxy(string $prompt): ?string
    {
        $url = config('fixit.ai.api_url');
        if (!$url) {
            return null;
        }

        try {
            $response = Http::timeout(config('fixit.ai.timeout', 10))
                ->post($url, ['prompt' => $prompt]);

            return $response->json('suggestion');
        } catch (\Exception $e) {
            logger()->error('Fixit AI proxy failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    protected function callOpenAiDirectly(string $prompt): ?string
    {
        $key = config('fixit.ai.api_key');
        if (!$key) {
            return null;
        }

        try {
            $response = Http::withToken($key)
                ->timeout(config('fixit.ai.timeout', 10))
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a Laravel error fix expert.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => 0.3,
                ]);

            return $response->json('choices.0.message.content');
        } catch (\Exception $e) {
            logger()->error('Fixit OpenAI failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    protected function buildPrompt(Throwable $exception): string
    {
        return <<<EOT
            Exception Message: {$exception->getMessage()}
            File: {$exception->getFile()}
            Line: {$exception->getLine()}

            Trace:
            {$exception->getTraceAsString()}

            What is the likely cause and how can it be fixed?
        EOT;
    }
}

