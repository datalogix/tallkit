<?php

namespace TALLKit\Components\Accordion;

use TALLKit\View\BladeComponent;

class Accordion extends BladeComponent
{
    public function __construct(
        public ?bool $exclusive = null,
        public null|bool|string $collapse = null,
        public ?string $variant = null,
        public ?string $size = null,
        public ?bool $bordered = null,
    ) {}
}
