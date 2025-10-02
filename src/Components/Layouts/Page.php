<?php

namespace TALLKit\Components\Layouts;

use TALLKit\View\BladeComponent;

class Page extends BladeComponent
{
    public function __construct(
        public ?string $breakpoint = null,
        public mixed $menu = null,
    ) {}
}
