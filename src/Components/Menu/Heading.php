<?php

namespace TALLKit\Components\Menu;

use TALLKit\View\BladeComponent;

class Heading extends BladeComponent
{
    public function __construct(
        public ?string $label = null,
        public ?string $size = null,
    ) {}
}
