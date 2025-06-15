<?php

namespace Fixit\Bootstrap;

class ValidateEncryptionConfig
{
    public static function handle(): void
    {
        if (config('fixit.encryption.enabled') && empty(config('fixit.encryption.key'))) {
            throw new \RuntimeException("FixIt encryption is enabled but FIXIT_ENCRYPTION_KEY is missing.");
        }
    }
}

