<?php

namespace Fixit\Commands;

use Illuminate\Console\Command;

class SyncFixitConfig extends Command
{
    // Command signature and options
    protected $signature = 'fixit:sync-config {--write : Append missing keys directly to config/fixit.php}';
    protected $description = 'Check for missing Fixit config keys. Use --write to append them to config/fixit.php.';

    /**
     * Run the config sync check.
     */
    public function handle()
    {
        $configPath = config_path('fixit.php');
        $defaultPath = __DIR__ . '/../../config/fixit.php';

        // Abort if user config doesn't exist
        if (!file_exists($configPath)) {
            $this->warn("config/fixit.php not found. Run:");
            $this->line("php artisan vendor:publish --tag=fixit-config");
            return;
        }

        // Load default config from package
        $defaultArray = include $defaultPath;
        if (!is_array($defaultArray)) {
            $this->error("❌ Failed to load default config from package.");
            return;
        }

        // Load current user config
        $userArray = include $configPath;
        if (!is_array($userArray)) {
            $this->error("❌ Failed to load user config: config/fixit.php must return an array.");
            return;
        }

        // Compare configs to find missing keys
        $missing = $this->findMissingKeys($userArray, $defaultArray);

        if (empty($missing)) {
            $this->info("✅ config/fixit.php is up to date.");
            return;
        }

        // Handle write or display mode
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

    /**
     * Return keys that are missing in the user config.
     */
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

    /**
     * Append missing keys to the config file with optional comments.
     */
    protected function appendToConfig(string $filePath, array $missingKeys): void
    {
        $original = file_get_contents($filePath);

        // Locate the final closing bracket in the config array
        $closingPos = strrpos($original, '];');
        if ($closingPos === false) {
            $this->error("❌ Could not find closing bracket of the config array.");
            return;
        }

        // Ensure a trailing comma before the closing bracket
        $beforeClosing = rtrim(substr($original, 0, $closingPos));
        if (!preg_match('/,\s*$/', $beforeClosing)) {
            $beforeClosing .= ',';
        }

        $afterClosing = substr($original, $closingPos);

        // Build new config content to insert
        $insertion = "\n\n";
        foreach ($missingKeys as $key => $value) {
            // Avoid duplicate keys
            if (preg_match('/[\'"]' . preg_quote($key, '/') . '[\'"]\s*=>/', $original)) {
                $this->warn("❌ '$key' already exists in config. Skipping...");
                continue;
            }

            $insertion .= $this->getRawConfigStringForKey($key) . "\n\n";
        }

        // Write updated config back to file
        $updated = $beforeClosing . $insertion . $afterClosing;
        file_put_contents($filePath, $updated);
    }

    /**
     * Get formatted config block for a missing key, with comments.
     */
    protected function getRawConfigStringForKey(string $key): string
    {
        $comment = $this->getCommentForKey($key);
        $formatted = $this->formatArray($key);
        return $comment . "\n" . $formatted;
    }

    /**
     * Return explanatory comments for known keys.
     */
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

    /**
     * Return the formatted PHP array string for a missing config key.
     */
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
                    'model' => env('FIXIT_AI_MODEL', 'gpt-3.5-turbo'),
                    'timeout' => env('FIXIT_AI_TIMEOUT', 10),
                ],
            PHP,
            default => "    '$key' => [],"
        };
    }
}
