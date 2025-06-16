<?php

namespace Fixit\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            \Fixit\FixItServiceProvider::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    protected function defineEnvironment($app)
    {
        $app['config']->set('fixit.notifications.send_on_error', false);
        $app['config']->set('fixit.encryption.enabled', false);
        $app['config']->set('fixit.encryption.key', 'base64:' . base64_encode(random_bytes(32)));
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}

