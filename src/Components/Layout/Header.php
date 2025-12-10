<?php

namespace TALLKit\Components\Layout;

use TALLKit\View\BladeComponent;

class Header extends BladeComponent
{
    public function __construct(
        public string|bool|null $appearance = null,
        public mixed $menu = null,
        public mixed $userMenu = null,
        public string|bool|null $align = null,
    ) {}
}
