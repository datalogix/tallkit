<?php

namespace TALLKit\Components\Button;

use Illuminate\View\ComponentSlot;
use TALLKit\View\BladeComponent;

class Button extends BladeComponent
{
    public function __construct(
        public string $type = 'button',
        public string $variant = 'outline',
        public ?string $text = null,
        public ?string $href = null,
        public ?string $size = null,
        public ?bool $circle = null,
        public ?bool $square = null,
        public ?string $kbd = null,
        public string|bool|null $loading = null,
        public string|ComponentSlot|null $icon = null,
        public string|ComponentSlot|null $iconTrailing = null,
    ) {}
}
