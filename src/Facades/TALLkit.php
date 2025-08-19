<?php

namespace TALLKit\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \TALLKit\TALLKit
 */
class TALLKit extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'tallkit';
    }
}
