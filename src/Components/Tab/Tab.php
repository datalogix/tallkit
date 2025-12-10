<?php

namespace TALLKit\Components\Tab;

use TALLKit\View\BladeComponent;

class Tab extends BladeComponent
{
    public function __construct(
        public ?string $name = null,
        public ?bool $selected = null,
    ) {}
}
