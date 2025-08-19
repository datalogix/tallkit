<?php

namespace TALLKit\Components\Button;

use TALLKit\Concerns\InteractsWithElement;
use TALLKit\View\BladeComponent;

class Button extends BladeComponent
{
    use InteractsWithElement;

    public function __construct(
        public ?string $type = null,
        public ?string $href = null,
        public string|bool|null $loading = null,
        public ?string $size = null,
        public ?bool $circle = null,
        public ?bool $square = null,
        public ?string $variant = null,
    ) {}
}
