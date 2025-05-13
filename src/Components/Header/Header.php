<?php

namespace TALLKit\Components\Header;

use TALLKit\View\BladeComponent;

class Header extends BladeComponent
{
    protected function props()
    {
        return [
            'sticky' => null,
            'container' => null,
        ];
    }
}
