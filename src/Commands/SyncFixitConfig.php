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
            $this->line("php artisan vendor:publish --tag=config");
            return;
        }

        $defaultRaw = file_get_contents($defaultPath);
        $defaultArray = include_once $defaultPath;
        $userArray = include_once $configPath;

        $missing = $this->findMissingKeys($userArray, $defaultArray);

        if (empty($missing)) {
            $this->info("✅ config/fixit.php is up to date.");
            return;
        }

        if ($this->option('write')) {
            $this->appendToConfig($configPath, $missing, $defaultRaw);
            $this->info("✅ Missing keys were appended to config/fixit.php.");
        } else {
            $this->warn("⚠️ Missing config keys detected:");
            foreach ($missing as $key => $value) {
                $this->line("
Add to `config/fixit.php`:
");
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

    protected function appendToConfig(string $filePath, array $missing, string $defaultRaw): void
    {
        $original = file_get_contents($filePath);

        $insertion = '';

        foreach (array_keys($missing) as $key) {
            if (preg_match("/\/\*.*?\*\/\s*['\"]{$key}['\"]\s*=>\s*\[[^\]]+\],/s", $defaultRaw, $match)) {
                $insertion .= "\n" . trim($match[0]) . "\n";
            } elseif (preg_match("/['\"]{$key}['\"]\s*=>\s*\[[^\]]+\],/s", $defaultRaw, $match)) {
                $insertion .= "\n    " . trim($match[0]) . "\n";
            } else {
                $this->warn("Couldn't extract raw block for config key: $key");
            }
        }

        $closingPos = strrpos($original, '];');
        if ($closingPos === false) {
            $this->error("Failed to locate end of config array.");
            return;
        }

        $updated = substr($original, 0, $closingPos) . rtrim($insertion, ",\n") . "\n" . substr($original, $closingPos);
        file_put_contents($filePath, $updated);
    }
}
