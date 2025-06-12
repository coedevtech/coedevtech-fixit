<?php

use Fixit\Tests\TestCase;

use function Pest\Laravel\config;

it('loads config values', function () {
    expect(app('config')->get('fixit.encryption.enabled'))->toBeFalse();
    expect(app('config')->get('fixit.notifications.send_on_error'))->toBeFalse();
    expect(app('config')->get('fixit.logging.table'))->toBe('fixit_errors');
})->uses(TestCase::class);

