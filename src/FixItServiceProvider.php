<?php

namespace Fixit;

use Fixit\Alerts\EmailAlert;
use Fixit\Alerts\SlackAlert;
use Fixit\Contracts\FixitAlertInterface;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\ServiceProvider;

class FixitServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        
        $this->publishes([
            __DIR__.'/../config/fixit.php' => config_path('fixit.php'),
        ], 'config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                \Fixit\Commands\InstallFixit::class,
                \Fixit\Commands\FixitStatus::class,
                \Fixit\Commands\FixitClear::class,
                \Fixit\Commands\PurgeOldLogs::class,
                \Fixit\Commands\FixitReportCommand::class,
            ]);
        }

        $handler = $this->app->make(ExceptionHandler::class);

        if (method_exists($handler, 'reportable')) {
            $handler->reportable(function (\Throwable $e) {
                app(\Fixit\Listeners\LogExceptionToDb::class)->handle($e);
            });
        }

        if (config('fixit.encryption.enabled') && empty(config('fixit.encryption.key'))) {
            throw new \RuntimeException("FixIt encryption is enabled but FIXIT_ENCRYPTION_KEY is missing.");
        }

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'fixit');
    }

    public function register()
    {
        $this->app->bind(FixitAlertInterface::class, function () {
            return match (config('fixit.notifications.driver')) {
                'slack' => new SlackAlert(),
                default => new EmailAlert(),
            };
        });

        $this->app->singleton('fixit', function () {
            return new \Fixit\Support\SecureEncryptor();
        });

        $this->mergeConfigFrom(__DIR__.'/../config/fixit.php', 'fixit');
    }
}


