<?php

namespace TALLKit\Components\Badge;

use Illuminate\View\ComponentSlot;
use TALLKit\View\BladeComponent;

class Badge extends BladeComponent
{
    public function __construct(
        public ?string $text = null,
        public ?string $size = null,
        public ?string $variant = null,
        public ?string $color = null,
        public string|ComponentSlot|null $icon = null,
        public string|ComponentSlot|null $iconTrailing = null,
        public string|bool|null $border = null,
    ) {}
}
