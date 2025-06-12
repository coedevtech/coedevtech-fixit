<?php

namespace Fixit\Contracts;

interface FixitAlertInterface
{
    public function send(string $message): void;
}


