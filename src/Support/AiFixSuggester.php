<?php

namespace Fixit\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Throwable;

class AiFixSuggester
{
    /**
     * Suggest a fix for the given exception using an AI provider.
     */
    public function suggest(Throwable $exception): ?string
    {
        // Exit early if AI suggestions are disabled in config
        if (!Config::get('fixit.ai.enabled')) {
            return null;
        }

        // Build the prompt from the exception details
        $prompt = $this->buildPrompt($exception);

        // Select the AI provider and call the appropriate handler
        $provider = Config::get('fixit.ai.provider', 'openai');

        return match ($provider) {
            'fixit-proxy' => $this->callProxy($prompt),
            'openai'      => $this->callOpenAi($prompt),
            'groq'        => $this->callGroq($prompt),
            'together'    => $this->callTogether($prompt),
            default       => null,
        };
    }

    /**
     * Call an internal or custom proxy API that returns a fix suggestion.
     */
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

    /**
     * Use OpenAI API to suggest a fix.
     */
    protected function callOpenAi(string $prompt): ?string
    {
        return $this->callChatCompletion(
            'https://api.openai.com/v1/chat/completions',
            config('fixit.ai.api_key'),
            config('fixit.ai.model', 'gpt-3.5-turbo'),
            $prompt,
            'Fixit OpenAI'
        );
    }

    /**
     * Use Groq API to suggest a fix.
     */
    protected function callGroq(string $prompt): ?string
    {
        return $this->callChatCompletion(
            'https://api.groq.com/openai/v1/chat/completions',
            config('fixit.ai.api_key'),
            config('fixit.ai.model', 'meta-llama/llama-4-scout-17b-16e-instruct'),
            $prompt,
            'Fixit Groq'
        );
    }

    /**
     * Use Together AI API to suggest a fix.
     */
    protected function callTogether(string $prompt): ?string
    {
        return $this->callChatCompletion(
            'https://api.together.xyz/v1/chat/completions',
            config('fixit.ai.api_key'),
            config('fixit.ai.model', 'mistralai/Mixtral-8x7B-Instruct-v0.1'),
            $prompt,
            'Fixit TogetherAI'
        );
    }

    /**
     * Generic method to call any AI chat completion endpoint.
     */
    protected function callChatCompletion(string $url, string $apiKey, string $model, string $prompt, string $logContext): ?string
    {
        if (!$apiKey) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type'  => 'application/json',
            ])
            ->timeout(config('fixit.ai.timeout', 10))
            ->post($url, [
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a Laravel error fix expert.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.3,
            ]);

            return $response->json('choices.0.message.content');
        } catch (\Exception $e) {
            logger()->error("{$logContext} API call failed", ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Construct a detailed prompt from the exception for AI input.
     */
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
