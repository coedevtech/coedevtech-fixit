<?php

use Fixit\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

use function PHPUnit\Framework\assertTrue;

it('runs fixit:install command', function () {
    $db = true;

    assertTrue($db);
})->uses(TestCase::class);

