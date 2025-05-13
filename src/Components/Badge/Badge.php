<?php

namespace TALLKit\Components\Badge;

use TALLKit\View\BladeComponent;

class Badge extends BladeComponent
{
    protected function props()
    {
        return [
            'variant' => null,
            'color' => null,
            'size' => null,
            'icon' => null,
            'border' => null,
        ];
    }
}
