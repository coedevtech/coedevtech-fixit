<?php

namespace Fixit\Bootstrap;

class RegisterCommands
{
    public static function get(): array
    {
        return [
            \Fixit\Commands\InstallFixit::class,
            \Fixit\Commands\FixitStatus::class,
            \Fixit\Commands\FixitClear::class,
            \Fixit\Commands\PurgeOldLogs::class,
            \Fixit\Commands\FixitReportCommand::class,
            \Fixit\Commands\SyncFixitConfig::class,
            \Fixit\Commands\SyncFixitMigrations::class,
        ];
    }
}
