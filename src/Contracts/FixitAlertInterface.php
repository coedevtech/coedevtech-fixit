<?php

namespace Fixit\Contracts;

use Throwable;

interface FixitAlertInterface
{
    public function send(string $message, ?Throwable $exception = null, ?string $suggestion = null): void;
}


