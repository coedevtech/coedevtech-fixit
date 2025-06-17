<?php 

namespace Fixit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class InstallFixit extends Command
{
    // Command signature and description shown in `php artisan list`
    protected $signature = 'fixit:install';
    protected $description = 'Install the FixIt error logger with optional encryption';

    /**
     * Execute the installation process for the FixIt package.
     */
    public function handle()
    {
        $this->info('🔧 Setting up FixIt package...');

        // Ask user if they want to enable encryption for error logs
        if ($this->confirm('Do you want to encrypt the error data saved in the DB?', true)) {
            $key = base64_encode(random_bytes(32)); // Generate a 256-bit key
            $envKey = 'FIXIT_ENCRYPTION_KEY';

            // Set relevant environment variables in .env file
            $this->setEnvValue($envKey, $key);
            $this->setEnvValue('FIXIT_ENCRYPTION_ENABLED', 'false');
            $this->setEnvValue('FIXIT_SEND_EMAIL', 'false');
            $this->setEnvValue('FIXIT_NOTIFICATION_EMAIL', 'email@example.com');
            $this->setEnvValue('FIXIT_AI_ENABLED', 'false');
            $this->setEnvValue('FIXIT_AI_PROVIDER', 'openai');
            $this->setEnvValue('FIXIT_AI_MODEL', 'gpt-3.5-turbo');
            $this->setEnvValue('FIXIT_AI_API_KEY', '');

            $this->info("🔐 Encryption enabled. Key added to .env as {$envKey}");
        } else {
            $this->info('❌ Encryption disabled.');
        }

        // Publish the FixIt config file to the host app
        if (!file_exists(config_path('fixit.php'))) {
            $this->call('vendor:publish', [
                '--provider' => "Fixit\\FixItServiceProvider",
                '--tag' => 'config'
            ]);
            $this->info('✅ Config file published to config/fixit.php');
        } else {
            $this->info('ℹ️ Config already exists. Skipped publishing.');
        }

        // Ask user if they want to run migrations immediately
        if ($this->confirm('Do you want to run the FixIt DB migration now?', true)) {
            $this->call('migrate');
            $this->info('📦 Migration completed.');
        } else {
            $this->info('📦 Migration skipped. You can run it manually using: php artisan migrate');
        }

        // ✅ NEW: Warn if QUEUE_CONNECTION=database but jobs table doesn't exist
        $queueDriver = config('queue.default');

        if ($queueDriver === 'database') {
            if (!Schema::hasTable('jobs')) {
                $this->warn('⚠️ Your QUEUE_CONNECTION is set to "database", but the "jobs" table does not exist.');
                $this->line('To fix this, run:');
                $this->line('  php artisan queue:table');
                $this->line('  php artisan migrate');
            } else {
                $this->info('✅ "jobs" table detected for database queue.');
            }
        }

        $this->info('✅ FixIt installation completed.');
    }

    /**
     * Add or update an environment variable in the .env file.
     */
    protected function setEnvValue($key, $value)
    {
        $envPath = base_path('.env');
        $content = file_get_contents($envPath);
        $pattern = "/^{$key}=.*$/m";

        // If the key already exists, update its value
        if (preg_match($pattern, $content)) {
            $content = preg_replace($pattern, "{$key}={$value}", $content);
        } else {
            // Otherwise, append the new key to the end of the file
            $content .= "\n{$key}={$value}\n";
        }

        // Write the updated content back to the .env file
        file_put_contents($envPath, $content);
    }
}
