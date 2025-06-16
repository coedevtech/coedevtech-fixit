<?php

namespace Fixit\Bootstrap;

class RegisterCommands
{
    /**
     * Return a list of Artisan commands provided by the Fixit package.
     * These will be registered only when running in console mode.
     */
    public static function get(): array
    {
        return [
            \Fixit\Commands\InstallFixit::class,        // Installs Fixit and publishes required assets
            \Fixit\Commands\FixitStatus::class,         // Displays the current status of Fixit
            \Fixit\Commands\FixitClear::class,          // Clears logged errors from the database
            \Fixit\Commands\PurgeOldLogs::class,        // Purges old or expired error logs
            \Fixit\Commands\FixitReportCommand::class,  // Generates a report of recent errors
            \Fixit\Commands\SyncFixitConfig::class,     // Re-syncs config with default values
            \Fixit\Commands\SyncFixitMigrations::class, // Re-syncs migrations with the application
        ];
    }
}
