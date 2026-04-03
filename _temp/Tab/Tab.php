<?php

namespace TALLKit\Components\Tab;

use TALLKit\Concerns\InteractsWithElement;
use TALLKit\View\BladeComponent;

class Tab extends BladeComponent
{
    use InteractsWithElement;

    public function __construct(
        public ?string $name = null,
        public ?bool $selected = null,
    ) {}
}
