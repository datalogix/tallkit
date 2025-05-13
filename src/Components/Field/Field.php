<?php

namespace TALLKit\Components\Field;

use TALLKit\View\BladeComponent;

class Field extends BladeComponent
{
    protected function props()
    {
        return [
            'name' => null,
            'id' => null,
            'label' => null,
            'description' => null,
            'help' => null,
        ];
    }
}
