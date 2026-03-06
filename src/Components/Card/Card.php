<?php

namespace TALLKit\Components\Card;

use Illuminate\View\ComponentSlot;
use TALLKit\View\BladeComponent;

class Card extends BladeComponent
{
    public function __construct(
        public ?string $size = null,
        public ?string $image = null,
        public ?string $alt = null,
        public ?ComponentSlot $header = null,
        public ?ComponentSlot $footer = null,

        // section
        public ?ComponentSlot $prepend = null,
        public string|ComponentSlot|null $title = null,
        public string|ComponentSlot|null $subtitle = null,
        public string|ComponentSlot|null $description = null,
        public ?ComponentSlot $append = null,
        public ?ComponentSlot $actions = null,
        public ?bool $separator = null,
        public string|ComponentSlot|null $content = null,
    ) {}
}
