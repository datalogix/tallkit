<?php

namespace TALLKit\Components\FullCalendar;

use Illuminate\Support\Str;
use TALLKit\Concerns\InteractsWithJsonOptions;
use TALLKit\View\BladeComponent;

class FullCalendar extends BladeComponent
{
    use InteractsWithJsonOptions;

    protected function getDefaultOptions()
    {
        return [
            'locale' => Str::lower(Str::replace('_', '-', app()->getLocale())),
        ];
    }
}
