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
        $default = require __DIR__ . '/../../config/fixit.php';

        if (!file_exists($configPath)) {
            $this->warn("config/fixit.php not found. Run:");
            $this->line("php artisan vendor:publish --tag=config");
            return;
        }

        $user = include $configPath;
        $missing = $this->findMissingKeys($user, $default);

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
                $this->line("    '$key' => " . $this->toShortArraySyntax($value) . ",");
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

    protected function toShortArraySyntax(array $array): string
    {
        $export = var_export($array, true);
        $export = preg_replace([
            '/^(\s*)array \(/m', '/\)(,?)$/m'
        ], ['[', ']$1'], $export);
        $export = preg_replace('/array \(/', '[', $export);
        return str_replace(')', ']', $export);
    }

    protected function appendToConfig(string $filePath, array $missing): void
    {
        $original = file_get_contents($filePath);

        // Insert before the closing `];`
        $closingPos = strrpos($original, '];');
        if ($closingPos === false) {
            $this->error("Failed to locate end of config array.");
            return;
        }

        $insertion = '';
        foreach ($missing as $key => $value) {
            $insertion .= "\n    '$key' => " . $this->toShortArraySyntax($value) . ",";
        }

        $updated = substr($original, 0, $closingPos) . rtrim($insertion, ',') . "\n" . substr($original, $closingPos);

        file_put_contents($filePath, $updated);
    }
}
