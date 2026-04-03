<?php

namespace TALLKit\Components\Sidebar;

use TALLKit\View\BladeComponent;

class Sidebar extends BladeComponent
{
    public function __construct(
        public ?string $name = null,
        public ?bool $sticky = null,
        public ?bool $stashable = null,
    ) {}
}
