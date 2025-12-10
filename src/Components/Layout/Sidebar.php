<?php

namespace TALLKit\Components\Layout;

use TALLKit\View\BladeComponent;

class Sidebar extends BladeComponent
{
    public function __construct(
        public ?bool $appearance = null,
        public mixed $menu = null,
        public mixed $userMenu = null,
    ) {}
}
