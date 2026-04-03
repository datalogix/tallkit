<?php

namespace TALLKit\Components\Alert;

use Illuminate\View\ComponentSlot;
use TALLKit\View\BladeComponent;

class Alert extends BladeComponent
{
    public function __construct(
        public ?string $type = null,
        public string|ComponentSlot|null $icon = null,
        public ?ComponentSlot $prepend = null,
        public string|ComponentSlot|null $title = null,
        public null|string|array $message = null,
        public ?ComponentSlot $append = null,
        public ?ComponentSlot $actions = null,
        public string|bool|null $border = null,
        public string|bool|ComponentSlot|null $dismissible = null,
        public int|bool|null $timeout = null,
        public ?string $size = null,
    ) {}
}
