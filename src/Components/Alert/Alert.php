<?php

namespace TALLKit\Components\Alert;

use Illuminate\View\ComponentSlot;
use TALLKit\View\BladeComponent;

class Alert extends BladeComponent
{
    public function __construct(
        public ?string $type = null,
        public string|bool|null $icon = null,
        public string|bool|null $border = null,
        public string|ComponentSlot|null $title = null,
        public ?string $message = null,
        public ?array $list = null,
        public string|bool|ComponentSlot|null $dismissible = null,
        public int|bool|null $timeout = null,
    ) {}
}
