<?php

namespace TALLKit\Components\Tab;

use TALLKit\View\BladeComponent;

class Items extends BladeComponent
{
    public function __construct(
        public ?string $size = null,
        public ?string $variant = null,
    ) {}
}
