<?php

namespace TALLKit\Components\Modal;

use Illuminate\Support\Str;
use TALLKit\Attributes\Mount;
use TALLKit\View\BladeComponent;

class Modal extends BladeComponent
{
    public function __construct(
        public ?string $name = null,
        public ?string $title = null,
        public ?string $subtitle = null,
        public ?string $size = null,
        public ?string $shortcut = null,
        public ?bool $dismissible = null,
        public null|string|bool $persist = null,
        public ?bool $closable = null,
        public ?string $position = null,
        public ?string $variant = null,
        public string|bool|null $backdrop = null,
    ) {}

    #[Mount()]
    public function mount()
    {
        $this->closable ??= $this->variant === 'bare' ? false : true;
        $this->name ??= Str::random();
    }
}
