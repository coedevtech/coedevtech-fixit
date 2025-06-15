<?php

namespace Fixit;

use Fixit\Alerts\EmailAlert;
use Fixit\Alerts\SlackAlert;
use Fixit\Contracts\FixitAlertInterface;
use Illuminate\Support\ServiceProvider;

class FixitServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'fixit');

        $this->publishes([
            __DIR__ . '/../config/fixit.php' => config_path('fixit.php'),
        ], 'config');

        if ($this->app->runningInConsole()) {
            $this->commands(\Fixit\Bootstrap\RegisterCommands::get());
        }
        
        \Fixit\Bootstrap\RegisterExceptionHandler::handle($this->app);
        \Fixit\Bootstrap\RunAutoFixCheck::handle();
        \Fixit\Bootstrap\ValidateEncryptionConfig::handle();
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


