<?php

namespace TALLKit\Components\Popover;

use TALLKit\View\BladeComponent;

class Popover extends BladeComponent
{
    public function __construct(
        public ?bool $keepOpen = null,
    ) {}
}
