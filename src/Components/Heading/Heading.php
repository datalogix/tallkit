<?php

namespace TALLKit\Components\Heading;

use TALLKit\View\BladeComponent;

class Heading extends BladeComponent
{
    public function __construct(
        public ?string $variant = null,
        public ?string $size = null,
    ) {}
}
