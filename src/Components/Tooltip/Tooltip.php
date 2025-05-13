<?php

namespace TALLKit\Components\Tooltip;

use TALLKit\View\BladeComponent;

class Tooltip extends BladeComponent
{
    protected function props()
    {
        return [
            'position' => 'top',
            'align' => 'center',
            'content' => null,
            'kbd' => null,
            'toggleable' => null,
        ];
    }
}
