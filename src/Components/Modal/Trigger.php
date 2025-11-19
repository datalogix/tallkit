<?php

namespace TALLKit\Components\Modal;

use TALLKit\View\BladeComponent;

class Trigger extends BladeComponent
{
    public function __construct(
        public ?string $name = null,
        public ?string $shortcut = null,
    ) {}
}
