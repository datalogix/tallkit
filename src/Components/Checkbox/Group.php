<?php

namespace TALLKit\Components\Checkbox;

use TALLKit\Concerns\InteractsWithField;
use TALLKit\Concerns\InteractsWithOptions;
use TALLKit\View\BladeComponent;

class Group extends BladeComponent
{
    use InteractsWithField;
    use InteractsWithOptions;

    public function __construct(
        public ?string $variant = null,
        public ?string $iconOn = null,
        public ?string $iconOff = null,
    ) {}
}
