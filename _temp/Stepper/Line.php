<?php

namespace TALLKit\Components\Stepper;

use TALLKit\View\BladeComponent;

class Line extends BladeComponent
{
    public function __construct(
        public ?string $size = null,
    ) {}
}
