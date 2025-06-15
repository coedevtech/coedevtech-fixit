<?php

namespace Fixit\Commands;

use Illuminate\Console\Command;

class SyncFixitConfig extends Command
{
    protected $signature = 'fixit:sync-config {--write : Append missing keys directly to config/fixit.php}';
    protected $description = 'Check for missing Fixit config keys. Use --write to append them to config/fixit.php.';

    public function handle()
    {
        $configPath = config_path('fixit.php');
        $defaultPath = __DIR__ . '/../../config/fixit.php';

        if (!file_exists($configPath)) {
            $this->warn("config/fixit.php not found. Run:");
            $this->line("php artisan vendor:publish --tag=fixit-config");
            return;
        }

        $defaultArray = include $defaultPath;
        if (!is_array($defaultArray)) {
            $this->error("❌ Failed to load default config from package.");
            return;
        }

        $userArray = include $configPath;
        if (!is_array($userArray)) {
            $this->error("❌ Failed to load user config: config/fixit.php must return an array.");
            return;
        }

        $missing = $this->findMissingKeys($userArray, $defaultArray);

        if (empty($missing)) {
            $this->info("✅ config/fixit.php is up to date.");
            return;
        }

        if ($this->option('write')) {
            $this->appendToConfig($configPath, $missing);
            $this->info("✅ Missing keys were appended to config/fixit.php.");
        } else {
            $this->warn("⚠️ Missing config keys detected:");
            foreach ($missing as $key => $value) {
                $this->line("\nAdd to `config/fixit.php`:\n");
                $this->line("    '$key' => [...],");
            }
        }
    }

    protected function findMissingKeys(array $user, array $default): array
    {
        $missing = [];
        foreach ($default as $key => $value) {
            if (!array_key_exists($key, $user)) {
                $missing[$key] = $value;
            }
        }
        return $missing;
    }

    protected function appendToConfig(string $filePath, array $missingKeys): void
    {
        $original = file_get_contents($filePath);

        // Add trailing comma if missing before the final `];`
        $closingPos = strrpos($original, '];');
        if ($closingPos === false) {
            $this->error("❌ Could not find closing bracket of the config array.");
            return;
        }

        $beforeClosing = rtrim(substr($original, 0, $closingPos));
        if (!preg_match('/,\s*$/', $beforeClosing)) {
            $beforeClosing .= ',';
        }

        $afterClosing = substr($original, $closingPos);

        $insertion = "\n\n";
        foreach ($missingKeys as $key => $value) {
            if (preg_match('/[\'"]' . preg_quote($key, '/') . '[\'"]\s*=>/', $original)) {
                $this->warn("❌ '$key' already exists in config. Skipping...");
                continue;
            }

            $insertion .= $this->getRawConfigStringForKey($key) . "\n\n";
        }

        $updated = $beforeClosing . $insertion . $afterClosing;
        file_put_contents($filePath, $updated);
    }

    protected function getRawConfigStringForKey(string $key): string
    {
        $comment = $this->getCommentForKey($key);
        $formatted = $this->formatArray($key);
        return $comment . "\n" . $formatted;
    }

    protected function getCommentForKey(string $key): string
    {
        return match ($key) {
            'auto_fix' => <<<EOT
                /*
                |--------------------------------------------------------------------------
                | Automatic Fix Status
                |--------------------------------------------------------------------------
                | Automatically marks old errors as "fixed" if they haven't reoccurred
                | in a defined number of days. This helps keep your error log clean
                | by closing stale issues.
                |
                | - `enabled`: Turns the feature on/off
                | - `check_interval_minutes`: How often the check should run (in minutes)
                | - `inactivity_days_to_fix`: Days without reoccurrence before marking as fixed
                */
            EOT,
                        'ai' => <<<EOT
                /*
                |--------------------------------------------------------------------------
                | AI-Powered Suggestions (Multi-provider Support)
                |--------------------------------------------------------------------------
                | Available providers:
                | - openai:      Uses OpenAI API (e.g. gpt-3.5-turbo, gpt-4)
                | - groq:        Uses Groq’s ultra-fast LLM API (e.g. mixtral-8x7b, llama3-70b)
                | - together:    Uses Together.ai’s hosted open models
                | - fixit-proxy: Custom internal proxy endpoint
                */
            EOT,
            default => ''
        };
    }

    protected function formatArray(string $key): string
    {
        return match ($key) {
            'auto_fix' => <<<PHP
                '$key' => [
                    'enabled' => true,
                    'check_interval_minutes' => 2,
                    'inactivity_days_to_fix' => 2,
                ],
            PHP,
                        'ai' => <<<PHP
                '$key' => [
                    'enabled' => env('FIXIT_AI_ENABLED', false),
                    'provider' => env('FIXIT_AI_PROVIDER', 'openai'),
                    'api_url' => env('FIXIT_AI_API_URL', null),
                    'api_key' => env('FIXIT_AI_API_KEY', null),
                    'model' => env('FIXIT_AI_MODEL', null),
                    'timeout' => env('FIXIT_AI_TIMEOUT', 10),
                ],
            PHP,
            default => "    '$key' => [],"
        };
    }
}
