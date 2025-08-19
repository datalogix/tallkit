<?php

namespace TALLKit\Components\Button;

use TALLKit\View\BladeComponent;

class Menu extends BladeComponent
{
    public function __construct(
        public mixed $items = null,
    ) {}
}
