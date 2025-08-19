<?php

namespace TALLKit\Components\Dropdown;

use TALLKit\View\BladeComponent;

class Dropdown extends BladeComponent
{
    public function __construct(
        public ?bool $hover = null,
        public ?string $position = null,
        public ?string $align = null,
    ) {}
}
