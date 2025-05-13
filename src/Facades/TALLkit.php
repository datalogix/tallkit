<?php

namespace TALLkit\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \TALLkit\TALLkit
 */
class TALLkit extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'tallkit';
    }
}
