<?php

namespace Fixit\Commands;

use Illuminate\Console\Command;

class SyncFixitConfig extends Command
{
    protected $signature = 'fixit:sync-config';
    protected $description = 'Sync missing Fixit config keys into config/fixit.php';

    public function handle()
    {
        $appConfigPath = config_path('fixit.php');

        /** @var array $defaultConfig */
        $defaultConfig = require __DIR__ . '/../../config/fixit.php';

        if (!file_exists($appConfigPath)) {
            $this->warn("config/fixit.php not found. Please publish it first using:");
            $this->line("php artisan vendor:publish --tag=config");
            return;
        }

        $userConfig = include $appConfigPath;
        $merged = $this->mergeMissingKeys($userConfig, $defaultConfig);
        $exported = "<?php\n\nreturn " . var_export($merged, true) . ";\n";
        file_put_contents($appConfigPath, $exported);

        $this->info("✅ Config synced: missing keys added to config/fixit.php");
    }

    /**
     * Recursively merge missing config keys
     *
     * @param array $user
     * @param array $default
     * @return array
     */
    protected function mergeMissingKeys(array $user, array $default): array
    {
        foreach ($default as $key => $value) {
            if (!array_key_exists($key, $user)) {
                $user[$key] = $value;
            } elseif (is_array($value) && is_array($user[$key])) {
                $user[$key] = $this->mergeMissingKeys($user[$key], $value);
            }
        }

        return $user;
    }
}
