<?php

namespace Fixit\Facades;

use Illuminate\Support\Facades\Facade;

class Fixit extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'fixit';
    }
}
