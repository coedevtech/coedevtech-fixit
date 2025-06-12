<?php 

namespace Fixit\Commands;

use Illuminate\Console\Command;

class InstallFixit extends Command
{
    protected $signature = 'fixit:install';
    protected $description = 'Install the FixIt error logger with optional encryption';

    public function handle()
    {
        $this->info('🔧 Setting up FixIt package...');

        if ($this->confirm('Do you want to encrypt the error data saved in the DB?', true)) {
            $key = base64_encode(random_bytes(32));
            $envKey = 'FIXIT_ENCRYPTION_KEY';
            $this->setEnvValue($envKey, $key);
            $this->setEnvValue('FIXIT_SEND_EMAIL', 'false');
            $this->setEnvValue('FIXIT_NOTIFICATION_EMAIL', 'admin@example.com');    

            $this->info("🔐 Encryption enabled. Key added to .env as {$envKey}");
        } else {
            $this->info('❌ Encryption disabled.');
        }

        if ($this->confirm('Do you want to run the FixIt DB migration now?', true)) {
            $this->call('migrate');
            $this->info('📦 Migration completed.');
        } else {
            $this->info('📦 Migration skipped. You can run it manually using: php artisan migrate');
        }

        $this->call('vendor:publish', [
            '--provider' => "Fixit\\FixitServiceProvider",
            '--tag' => 'config'
        ]);

        $this->info('✅ FixIt installation completed.');
    }

    protected function setEnvValue($key, $value)
    {
        $envPath = base_path('.env');
        $content = file_get_contents($envPath);
        $pattern = "/^{$key}=.*$/m";

        if (preg_match($pattern, $content)) {
            $content = preg_replace($pattern, "{$key}={$value}", $content);
        } else {
            $content .= "\n{$key}={$value}\n";
        }

        file_put_contents($envPath, $content);
    }
}

