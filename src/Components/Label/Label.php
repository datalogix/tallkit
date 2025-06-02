<?php

namespace TALLKit\Components\Label;

use Illuminate\View\ComponentSlot;
use TALLKit\View\BladeComponent;

class Label extends BladeComponent
{
    public function __construct(
        public ?string $text = null,
        public string|ComponentSlot|null $badge = null,
    ) {}
}
