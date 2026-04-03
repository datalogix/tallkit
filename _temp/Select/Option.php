<?php

namespace TALLKit\Components\Select;

use TALLKit\View\BladeComponent;

class Option extends BladeComponent
{
    public function __construct(
        public mixed $label = null,
        public mixed $value = null,
        public ?bool $selected = null,
    ) {}
}
