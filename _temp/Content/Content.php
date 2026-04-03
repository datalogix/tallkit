<?php

namespace TALLKit\Components\Content;

use Illuminate\View\ComponentSlot;
use TALLKit\View\BladeComponent;

class Content extends BladeComponent
{
    public function __construct(
        public ?string $size = null,
        public string|ComponentSlot|null $icon = null,
        public ?ComponentSlot $prepend = null,
        public string|ComponentSlot|null $title = null,
        public null|string|array $description = null,
        public ?ComponentSlot $append = null,
        public ?ComponentSlot $actions = null,
    ) {}
}
