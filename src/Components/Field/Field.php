<?php

namespace TALLKit\Components\Field;

use TALLKit\View\BladeComponent;

class Field extends BladeComponent
{
    public function __construct(
        public ?string $variant = null,
        public ?string $align = null,
    ) {}
}
