<?php

namespace TALLKit\Components\Menu;

use TALLKit\View\BladeComponent;

class Menu extends BladeComponent
{
    public function __construct(
        public ?bool $keepOpen = null,
    ) {}
}
