<?php

namespace Fixit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SyncFixitMigrations extends Command
{
    protected $signature = 'fixit:sync-migrations';
    protected $description = 'Publish and run new Fixit migrations if not already present';

    public function handle()
    {
        $this->publishMigrations();
        $this->runMigrations();
    }

    protected function publishMigrations(): void
    {
        $migrationPath = database_path('migrations');
        $packageMigrationPath = __DIR__ . '/../../database/migrations';

        $files = File::files($packageMigrationPath);

        foreach ($files as $file) {
            $filename = $file->getFilename();

            $exists = collect(File::files($migrationPath))->contains(function ($existing) use ($filename) {
                return str_ends_with($existing->getFilename(), $filename);
            });

            if (! $exists) {
                $timestamp = now()->format('Y_m_d_His');
                $targetPath = "$migrationPath/{$timestamp}_$filename";
                File::copy($file->getRealPath(), $targetPath);
                $this->info("🟢 Published migration: $filename");
            } else {
                $this->line("⏭️  Skipped existing migration: $filename");
            }
        }
    }

    protected function runMigrations(): void
    {
        $this->call('migrate', ['--force' => true]);
        $this->info("✅ New Fixit migrations have been run.");
    }
}
