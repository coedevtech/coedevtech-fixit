<?php

namespace Fixit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SyncFixitMigrations extends Command
{
    // Artisan command signature and description
    protected $signature = 'fixit:sync-migrations';
    protected $description = 'Publish and run new Fixit migrations if not already present';

    /**
     * Handle the command execution.
     */
    public function handle()
    {
        // Step 1: Publish any new migrations that aren't already in the app
        $this->publishMigrations();

        // Step 2: Run all pending migrations
        $this->runMigrations();
    }

    /**
     * Copy new Fixit migration files to the app's database/migrations directory.
     * Ensures migrations are not duplicated by checking filename endings.
     */
    protected function publishMigrations(): void
    {
        $migrationPath = database_path('migrations');
        $packageMigrationPath = __DIR__ . '/../../database/migrations';

        $files = File::files($packageMigrationPath);

        foreach ($files as $file) {
            $filename = $file->getFilename();

            // Check if a migration file with this name already exists in the app
            $exists = collect(File::files($migrationPath))->contains(function ($existing) use ($filename) {
                return str_ends_with($existing->getFilename(), $filename);
            });

            if (! $exists) {
                // Prefix with current timestamp to make it unique
                $timestamp = now()->format('Y_m_d_His');
                $targetPath = "$migrationPath/{$timestamp}_$filename";

                File::copy($file->getRealPath(), $targetPath);
                $this->info("🟢 Published migration: $filename");
            } else {
                $this->line("⏭️  Skipped existing migration: $filename");
            }
        }
    }

    /**
     * Run all pending migrations using Artisan's migrate command.
     */
    protected function runMigrations(): void
    {
        // Force migration without confirmation
        $this->call('migrate', ['--force' => true]);
        $this->info("✅ New Fixit migrations have been run.");
    }
}
