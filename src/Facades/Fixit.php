<?php

namespace Fixit\Facades;

use Illuminate\Support\Facades\Facade;

class Fixit extends Facade
{
    /**
     * Get the registered name of the component in the Laravel container.
     *
     * This allows `Fixit::encrypt()` and similar static calls to resolve
     * the 'fixit' singleton (usually an instance of SecureEncryptor).
     */
    protected static function getFacadeAccessor(): string
    {
        return 'fixit';
    }
}
