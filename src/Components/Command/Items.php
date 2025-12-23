<?php

namespace TALLKit\Components\Command;

use TALLKit\View\BladeComponent;

class Items extends BladeComponent
{
    public function __construct(
        public mixed $items = null,
    ) {}
}
