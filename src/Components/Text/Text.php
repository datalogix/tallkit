<?php

namespace TALLKit\Components\Text;

use TALLKit\View\BladeComponent;

class Text extends BladeComponent
{
    protected function props()
    {
        return [
            'inline' => false,
            'variant' => null,
            'color' => null,
            'size' => null,
        ];
    }
}
