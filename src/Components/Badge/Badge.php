<?php

namespace TALLKit\Components\Badge;

use TALLKit\Concerns\InteractsWithElement;
use TALLKit\View\BladeComponent;

class Badge extends BladeComponent
{
    use InteractsWithElement;

    public function __construct(
        public ?string $size = null,
        public ?string $variant = null,
        public string|bool|null $border = null,
        public ?bool $pill = null,
        public ?bool $solid = null,
        public ?bool $close = null,
    ) {}
}
