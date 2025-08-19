<?php

namespace TALLKit\Components\Sidebar;

use TALLKit\View\BladeComponent;

class Toggle extends BladeComponent
{
    public function __construct(
        public ?string $name = null
    ) {}
}
