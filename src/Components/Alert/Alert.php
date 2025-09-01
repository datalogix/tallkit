<?php

namespace TALLKit\Components\Alert;

use Illuminate\View\ComponentSlot;
use TALLKit\Attributes\Mount;
use TALLKit\View\BladeComponent;

class Alert extends BladeComponent
{
    public function __construct(
        public null|string|array $message = null,
        public ?string $type = null,
        public string|bool|null $icon = null,
        public string|bool|null $border = null,
        public string|ComponentSlot|null $title = null,
        public ?array $list = null,
        public string|bool|ComponentSlot|null $dismissible = null,
        public int|bool|null $timeout = null,
        public ?string $size = null,
    ) {}

    #[Mount()]
    protected function mount()
    {
        $this->list ??= is_array($this->message) ? $this->message : null;
    }
}
