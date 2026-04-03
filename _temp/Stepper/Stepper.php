<?php

namespace TALLKit\Components\Stepper;

use TALLKit\View\BladeComponent;

class Stepper extends BladeComponent
{
    public function __construct(
        public ?int $current = null,
        public mixed $steps = null,
        public ?string $size = null,
        public ?string $iconCompleted = null,
        public ?string $iconActive = null,
    ) {}
}
