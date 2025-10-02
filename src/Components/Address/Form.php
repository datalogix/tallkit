<?php

namespace TALLKit\Components\Address;

use TALLKit\View\BladeComponent;

class Form extends BladeComponent
{
    public function __construct(
        public ?bool $autocomplete = null,
        public ?bool $required = null,
    ) {}
}
