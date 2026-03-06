<?php

namespace TALLKit\Components\Modal;

use Illuminate\View\ComponentSlot;
use TALLKit\View\BladeComponent;

class Confirm extends BladeComponent
{
    public function __construct(
        public ?string $size = null,
        public ?string $confirm = null,
        public ?string $cancel = null,
        public ?string $variant = null,
        public ?bool $autoClose = null,

        // section
        public ?ComponentSlot $prepend = null,
        public string|ComponentSlot|null $title = null,
        public string|ComponentSlot|null $subtitle = null,
        public string|ComponentSlot|null $description = null,
        public ?ComponentSlot $append = null,
        public ?ComponentSlot $actions = null,
        public string|ComponentSlot|null $content = null,
    ) {}
}
