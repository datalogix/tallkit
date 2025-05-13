<?php

namespace TALLKit\Components\BackButton;

use TALLKit\View\BladeComponent;

class BackButton extends BladeComponent
{
    protected function props()
    {
        return [
            'href' => url()->previous(),
        ];
    }
}
