<?php

namespace TALLKit\Components\Error;

use TALLKit\View\BladeComponent;

class Group extends BladeComponent
{
    public function __construct(
        public ?string $bag = null,
    ) {}
}
