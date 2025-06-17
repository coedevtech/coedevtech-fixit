<?php

namespace Fixit\Contracts;

interface FixitAlertInterface
{
    /**
     * Send an alert when an exception occurs.
     *
     * @param string         $message     Summary of the error
     * @param string|null    $exception   Optional exception object (not used here)
     * @param string|null    $suggestion  Optional AI-generated suggestion
     * @param int|null       $occurrences Optional
     * @param string|null    $date        Optional
     * @param string|null    $environment Optional
     */
    public function send(
        string $message,
        ?string $exception = null,
        ?string $suggestion = null,
        ?int $occurrences = null,
        ?string $date = null,
        ?string $environment = null
    ): void;
}
