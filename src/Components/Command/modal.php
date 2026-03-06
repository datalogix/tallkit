<?php

namespace TALLKit\Components\Command;

use TALLKit\View\BladeComponent;

class Modal extends BladeComponent
{
    public function __construct(
        public ?string $shortcut = null,
        public ?bool $focusOnOpen = null,
        public ?bool $closeOnSelect = null,
    ) {}
}
