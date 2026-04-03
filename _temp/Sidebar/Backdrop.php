<?php

namespace TALLKit\Components\Sidebar;

use TALLKit\View\BladeComponent;

class Backdrop extends BladeComponent
{
    public function __construct(
        public ?string $name = null
    ) {}
}
