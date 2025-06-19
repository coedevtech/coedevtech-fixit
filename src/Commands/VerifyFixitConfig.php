<?php

namespace Fixit\Commands;

use Illuminate\Console\Command;

class VerifyFixitConfig extends Command
{
    protected $signature = 'fixit:verify-config 
                            {--fix : Automatically append missing .env keys} 
                            {--json : Output results as JSON for CI pipelines}';

    protected $description = 'Verify FixIt configuration and optionally auto-fix missing environment values.';

    public function handle(): int
    {
        $this->info('🔍 Verifying FixIt configuration...');

        $emails = $this->validateEmails();
        $missingEnv = $this->detectMissingEnv();

        if (!empty($missingEnv)) {
            $this->warnMissingEnv($missingEnv);

            if ($this->option('fix')) {
                $this->appendToEnvFile($missingEnv);
                $this->info('✅ .env file updated with missing keys.');
            } else {
                $this->line("💡 Tip: Run with --fix to automatically append missing keys.");
            }
        } else {
            $this->line('✅ All required .env keys are present.');
        }

        if ($this->option('json')) {
            return $this->outputJson([
                'emails' => $emails,
                'missing_env_keys' => $missingEnv,
                'encryption_enabled' => config('fixit.encryption.enabled'),
                'slack_webhook' => config('fixit.notifications.slack_webhook'),
                'ai_enabled' => config('fixit.ai.enabled'),
            ]);
        }

        $this->info('✅ FixIt configuration check complete.');
        return self::SUCCESS;
    }

    /**
     * Validate and return the list of valid emails.
     */
    protected function validateEmails(): array
    {
        $raw = config('fixit.notifications.email');
        $emails = is_array($raw) ? $raw : array_map('trim', explode(',', $raw));
        $validEmails = array_filter($emails, fn($e) => filter_var($e, FILTER_VALIDATE_EMAIL));

        if (empty($validEmails)) {
            $this->error('❌ No valid email recipients defined in fixit.notifications.email');
        } else {
            $this->line('✅ Email recipients configured: ' . implode(', ', $validEmails));
        }

        return $validEmails;
    }

    /**
     * Detect missing .env keys based on FixIt features.
     */
    protected function detectMissingEnv(): array
    {
        return array_merge(
            $this->checkEncryptionEnv(),
            $this->checkNotificationEnv(),
            $this->checkAutoFixEnv(),
            $this->checkAiEnv()
        );
    }

    /**
     * Display missing .env keys to the user.
     */
    protected function warnMissingEnv(array $missing): void
    {
        $this->warn("⚠️  Missing .env keys detected:");
        foreach ($missing as $key => $value) {
            $this->line("    $key=$value");
        }
    }

    /**
     * Append missing keys to the .env file.
     */
    protected function appendToEnvFile(array $entries): void
    {
        $envPath = base_path('.env');

        if (!file_exists($envPath)) {
            $this->error("❌ .env file not found at: $envPath");
            return;
        }

        $envContent = file_get_contents($envPath);

        foreach ($entries as $key => $value) {
            if (!str_contains($envContent, "$key=")) {
                file_put_contents($envPath, PHP_EOL . "$key=$value", FILE_APPEND);
            }
        }
    }

    /**
     * Output summary as JSON (for CI pipelines).
     */
    protected function outputJson(array $summary): int
    {
        $this->line(json_encode($summary, JSON_PRETTY_PRINT));
        return self::SUCCESS;
    }

    protected function checkEncryptionEnv(): array
    {
        $missing = [];

        if (!env('FIXIT_ENCRYPTION_ENABLED')) {
            $missing['FIXIT_ENCRYPTION_ENABLED'] = 'false';
        }

        if (!env('FIXIT_ENCRYPTION_KEY')) {
            $missing['FIXIT_ENCRYPTION_KEY'] = 'base64:' . base64_encode(random_bytes(32));
        }

        return $missing;
    }

    protected function checkNotificationEnv(): array
    {
        $missing = [];

        if (!env('FIXIT_NOTIFICATION_DRIVER')) {
            $missing['FIXIT_NOTIFICATION_DRIVER'] = 'email';
        }

        if (!env('FIXIT_SEND_EMAIL')) {
            $missing['FIXIT_SEND_EMAIL'] = 'false';
        }

        if (!env('FIXIT_NOTIFICATION_EMAIL')) {
            $missing['FIXIT_NOTIFICATION_EMAIL'] = 'email@example.com';
        }

        if (!env('FIXIT_ALLOW_MULTIPLE_EMAILS')) {
            $missing['FIXIT_ALLOW_MULTIPLE_EMAILS'] = 'false';
        }

        if (config('fixit.notifications.driver') === 'slack' && !env('FIXIT_SLACK_WEBHOOK')) {
            $missing['FIXIT_SLACK_WEBHOOK'] = 'https://hooks.slack.com/services/your/webhook/url';
        }

        return $missing;
    }

    protected function checkAutoFixEnv(): array
    {
        $missing = [];

        if (!env('FIXIT_AUTO_FIX_ENABLED')) {
            $missing['FIXIT_AUTO_FIX_ENABLED'] = 'true';
        }

        if (!env('FIXIT_AUTO_FIX_CHECK_INTERVAL_MINUTES')) {
            $missing['FIXIT_AUTO_FIX_CHECK_INTERVAL_MINUTES'] = '2';
        }

        if (!env('FIXIT_AUTO_FIX_INACTIVITY_DAYS_TO_FIX')) {
            $missing['FIXIT_AUTO_FIX_INACTIVITY_DAYS_TO_FIX'] = '30';
        }

        return $missing;
    }

    protected function checkAiEnv(): array
    {
        $missing = [];

        if (!env('FIXIT_AI_ENABLED')) {
            $missing['FIXIT_AI_ENABLED'] = 'false';
        }

        if (!env('FIXIT_AI_PROVIDER')) {
            $missing['FIXIT_AI_PROVIDER'] = 'openai';
        }

        if (!env('FIXIT_AI_MODEL')) {
            $missing['FIXIT_AI_MODEL'] = 'gpt-3.5-turbo';
        }

        if (config('fixit.ai.enabled') && !env('FIXIT_AI_API_KEY')) {
            $missing['FIXIT_AI_API_KEY'] = 'sk-your-api-key';
        }

        if (config('fixit.ai.provider') === 'fixit-proxy' && !env('FIXIT_AI_API_URL')) {
            $missing['FIXIT_AI_API_URL'] = 'https://your-fixit-proxy/api';
        }

        if (!env('FIXIT_AI_TIMEOUT')) {
            $missing['FIXIT_AI_TIMEOUT'] = '10';
        }

        return $missing;
    }
}
