<?php

namespace TALLKit\Components\Layouts;

use TALLKit\View\BladeComponent;

class AppHeader extends BladeComponent
{
    public function __construct(
        public ?bool $appearance = null,
        public mixed $menu = null,
        public mixed $userMenu = null,
    ) {}
}
