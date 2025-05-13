<?php

namespace TALLKit\Components\InputSwitch;

use TALLKit\View\BladeComponent;

class InputSwitch extends BladeComponent
{
    public static array $aliases = ['switch'];

    protected function props()
    {
        return [
            'name' => null,
            'label' => null,
            'id' => null,
            'description' => null,
            'help' => null,
        ];
    }
}
