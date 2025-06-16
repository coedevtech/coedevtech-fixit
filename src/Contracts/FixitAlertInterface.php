<?php

namespace Fixit\Contracts;

use Throwable;

interface FixitAlertInterface
{
    /**
     * Send an alert when an exception occurs.
     *
     * @param string         $message     A short message or summary of the issue
     * @param Throwable|null $exception   Optional exception object for detailed context
     * @param string|null    $suggestion  Optional AI-generated fix suggestion
     */
    public function send(string $message, ?Throwable $exception = null, ?string $suggestion = null): void;
}
