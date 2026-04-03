<?php

namespace TALLKit\Components\Form;

use Illuminate\View\ComponentSlot;
use TALLKit\View\BladeComponent;

class Card extends BladeComponent
{
    public function __construct(
        public ?string $size = null,
        public ?ComponentSlot $header = null,
        public ?ComponentSlot $prepend = null,
        public string|ComponentSlot|null $title = null,
        public string|ComponentSlot|null $subtitle = null,
        public string|ComponentSlot|null $description = null,
        public ?ComponentSlot $append = null,
        public ?ComponentSlot $actions = null,
        public ?ComponentSlot $footer = null,
    ) {}
}
