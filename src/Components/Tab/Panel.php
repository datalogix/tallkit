<?php

namespace TALLKit\Components\Tab;

use TALLKit\View\BladeComponent;

class Panel extends BladeComponent
{
    public function __construct(
        public string $name,
        public ?bool $selected = null,
    ) {}
}
