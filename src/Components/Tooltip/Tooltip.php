<?php

namespace TALLKit\Components\Tooltip;

use Illuminate\View\ComponentSlot;
use TALLKit\View\BladeComponent;

class Tooltip extends BladeComponent
{
    public function __construct(
        public ?string $position = null,
        public ?string $align = null,
        public string|ComponentSlot|null $content = null,
        public string|ComponentSlot|null $kbd = null,
        public ?string $mode = null,
        public ?string $variant = null,
        public ?string $size = null,
        public null|bool|string $arrow = null,
    ) {}
}
