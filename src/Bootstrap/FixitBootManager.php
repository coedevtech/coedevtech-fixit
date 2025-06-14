<?php

namespace Fixit\Bootstrap;

use Fixit\Commands\{
    FixitClear,
    FixitReportCommand,
    FixitStatus,
    InstallFixit,
    PurgeOldLogs,
    SyncFixitConfig,
    SyncFixitMigrations
};
use Fixit\Enum\ErrorStatus;
use Fixit\Models\FixitError;
use Illuminate\Contracts\Debug\ExceptionHandler;

class FixitBootManager
{
    public static function boot(): void
    {
        $app = app();

        // Register Artisan commands
        if ($app->runningInConsole()) {
            $app->singleton('Illuminate\\Console\\Application', function () use ($app) {
                return tap($app['artisan'], function ($artisan) use ($app) {
                    $artisan->addCommands([
                        new InstallFixit(),
                        new FixitStatus(),
                        new FixitClear(),
                        new PurgeOldLogs(),
                        new FixitReportCommand(),
                        new SyncFixitConfig(),
                        new SyncFixitMigrations(),
                    ]);
                });
            });
        }

        // Register exception handler
        $handler = $app->make(ExceptionHandler::class);
        if (method_exists($handler, 'reportable')) {
            $handler->reportable(function (\Throwable $e) {
                app(\Fixit\Listeners\LogExceptionToDb::class)->handle($e);
            });
        }

        // Auto-fix checker
        if (!$app->runningInConsole() && config('fixit.auto_fix.enabled')) {
            $key = 'fixit:auto-status-check';

            if (!cache()->has($key)) {
                $days = config('fixit.auto_fix.inactivity_days_to_fix', 2);

                FixitError::where('status', ErrorStatus::NOT_FIXED->value)
                    ->where('last_seen_at', '<', now()->subDays($days))
                    ->update(['status' => ErrorStatus::FIXED->value]);

                cache()->put($key, now(), now()->addMinutes(
                    config('fixit.auto_fix.check_interval_minutes', 10)
                ));
            }
        }

        // Encryption validation
        if (config('fixit.encryption.enabled') && empty(config('fixit.encryption.key'))) {
            throw new \RuntimeException("FixIt encryption is enabled but FIXIT_ENCRYPTION_KEY is missing.");
        }

        // Load views
        $app->make('view')->addNamespace('fixit', __DIR__ . '/../../resources/views');
    }
}
