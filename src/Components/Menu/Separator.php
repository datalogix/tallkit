<?php

namespace TALLKit\Components\Menu;

use TALLKit\View\BladeComponent;

class Separator extends BladeComponent
{
    public function __construct(
        public ?string $variant = null,
        public ?string $label = null,
    ) {}
}
