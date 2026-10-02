<?php

namespace TALLKit\Facades;

use Illuminate\Support\Facades\Facade;

class TALLKit extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'tallkit';
    }
}
