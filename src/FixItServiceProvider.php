<?php

namespace Fixit;

use Fixit\Alerts\EmailAlert;
use Fixit\Alerts\SlackAlert;
use Fixit\Contracts\FixitAlertInterface;
use Illuminate\Support\ServiceProvider;

class FixitServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services after all other services have been registered.
     */
    public function boot()
    {
        // Load package migrations so users can publish or run them
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Load views with a namespace so they can be referenced as `fixit::view-name`
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'fixit');

        // Allow config file to be published to the host application's config directory
        $this->publishes([
            __DIR__ . '/../config/fixit.php' => config_path('fixit.php'),
        ], 'config');

        // Register custom Artisan commands if running in CLI
        if ($this->app->runningInConsole()) {
            $this->commands(\Fixit\Bootstrap\RegisterCommands::get());
        }

        // Register Fixit’s custom exception handler for automatic error processing
        \Fixit\Bootstrap\RegisterExceptionHandler::handle($this->app);

        // Run automatic error fix checker (based on configuration or schedule)
        \Fixit\Bootstrap\RunAutoFixCheck::handle();

        // Ensure encryption config is valid for secure storage
        \Fixit\Bootstrap\ValidateEncryptionConfig::handle();
    }

    /**
     * Register bindings in the container.
     */
    public function register()
    {
        // Dynamically bind the alert driver (Slack or Email) based on config
        $this->app->bind(FixitAlertInterface::class, function () {
            return match (config('fixit.notifications.driver')) {
                'slack' => new SlackAlert(),
                default => new EmailAlert(),
            };
        });

        // Register a singleton for secure encryption logic under the 'fixit' key
        $this->app->singleton('fixit', function () {
            return new \Fixit\Support\SecureEncryptor();
        });

        // Merge default config to allow user override while keeping fallbacks
        $this->mergeConfigFrom(__DIR__ . '/../config/fixit.php', 'fixit');
    }
}
