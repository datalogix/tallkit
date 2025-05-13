<?php

namespace TALLKit\Components\Button;

use TALLKit\View\BladeComponent;

class Button extends BladeComponent
{
    protected function props()
    {
        return [
            'text' => null,
        ];
    }
}
