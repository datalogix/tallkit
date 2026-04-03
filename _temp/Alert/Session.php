<?php

namespace TALLKit\Components\Alert;

use TALLKit\View\BladeComponent;

class Session extends BladeComponent
{
    public function __construct(
        public string $name = 'status'
    ) {}
}
