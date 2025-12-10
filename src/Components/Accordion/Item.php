<?php

namespace TALLKit\Components\Accordion;

use Illuminate\View\ComponentSlot;
use TALLKit\View\BladeComponent;

class Item extends BladeComponent
{
    public function __construct(
        public null|string|ComponentSlot $heading = null,
        public ?bool $expanded = null,
        public ?bool $disabled = null,
    ) {}
}
