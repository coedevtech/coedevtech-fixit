<?php

namespace Fixit\Bootstrap;

class ValidateEncryptionConfig
{
    /**
     * Validates that the encryption key is present when encryption is enabled.
     *
     * This prevents runtime errors by ensuring required config is in place early.
     */
    public static function handle(): void
    {
        // If encryption is enabled but no key is set, throw a runtime exception
        if (config('fixit.encryption.enabled') && empty(config('fixit.encryption.key'))) {
            throw new \RuntimeException("FixIt encryption is enabled but FIXIT_ENCRYPTION_KEY is missing.");
        }
    }
}
