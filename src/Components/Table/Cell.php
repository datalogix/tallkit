<?php

namespace TALLKit\Components\Table;

use TALLKit\View\BladeComponent;

class Cell extends BladeComponent
{
    protected function props()
    {
        return [
            'value' => null,
        ];
    }
}
