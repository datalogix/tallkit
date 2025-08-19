<?php

namespace TALLKit\Components\Auth\Layouts;

use TALLKit\View\BladeComponent;

class Simple extends BladeComponent
{
    public function __construct(
        public bool $appearance = true,
    ) {}
}
