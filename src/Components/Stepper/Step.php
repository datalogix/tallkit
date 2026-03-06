<?php

namespace TALLKit\Components\Stepper;

use TALLKit\Concerns\InteractsWithElement;
use TALLKit\View\BladeComponent;

class Step extends BladeComponent
{
    use InteractsWithElement;

    public function __construct(
        public ?int $index = null,
        public ?string $status = null,
        public ?string $iconCompleted = null,
        public ?string $iconActive = null,
    ) {}
}
