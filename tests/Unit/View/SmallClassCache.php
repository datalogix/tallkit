<?php

namespace TALLKit\Tests\Unit\View;

use TALLKit\View\ClassCache;

class SmallClassCache extends ClassCache
{
    protected const MAX_ENTRIES = 2;

    public static function state(): array
    {
        return ['merged' => static::$merged, 'pending' => static::$pending, 'loaded' => static::$loaded];
    }
}
