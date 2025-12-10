<?php

namespace TALLKit\Components\Card;

use Illuminate\View\ComponentSlot;
use TALLKit\View\BladeComponent;

class Card extends BladeComponent
{
    public function __construct(
        public string|ComponentSlot|null $title = null,
        public string|ComponentSlot|null $subtitle = null,
        public ?bool $separator = null,
        public ?string $size = null,
        public string|ComponentSlot|null $content = null,
        public ?string $image = null,
        public ?string $alt = null,
    ) {}
}
