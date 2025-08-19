<?php

namespace TALLKit\Components\Table\Cell;

use TALLKit\View\BladeComponent;

class Cell extends BladeComponent
{
    public function __construct(
        public ?string $align = null,
        public ?string $label = null
    ) {}
}
