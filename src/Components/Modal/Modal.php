<?php

namespace TALLKit\Components\Modal;

use Illuminate\Support\Str;
use Illuminate\View\ComponentSlot;
use TALLKit\Attributes\Mount;
use TALLKit\View\BladeComponent;

class Modal extends BladeComponent
{
    public function __construct(
        public ?string $size = null,
        public ?string $name = null,
        public ?string $shortcut = null,
        public ?bool $dismissible = null,
        public null|string|bool $persist = null,
        public ?bool $closable = null,
        public ?string $position = null,
        public ?string $variant = null,
        public string|bool|null $backdrop = null,
        public ?ComponentSlot $trigger = null,

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

    #[Mount()]
    public function mount()
    {
        $this->name ??= Str::random();
        $this->closable ??= $this->variant === 'bare' ? false : true;
    }
}
