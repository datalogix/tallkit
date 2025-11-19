<?php

namespace TALLKit\Components\Nav;

use TALLKit\View\BladeComponent;

class Menu extends BladeComponent
{
    public function __construct(
        public mixed $items = null,
        public ?string $size = null,
    ) {}
}
