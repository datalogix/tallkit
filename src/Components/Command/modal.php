<?php

namespace TALLKit\Components\Command;

use TALLKit\View\BladeComponent;

class Modal extends BladeComponent
{
    public function __construct(
        public ?bool $focusOnOpen = null,
    ) {}
}
