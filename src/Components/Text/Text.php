<?php

namespace TALLKit\Components\Text;

use TALLKit\View\BladeComponent;

class Text extends BladeComponent
{
    public function __construct(
        public ?string $value = null,
        public ?string $variant = null,
        public ?string $size = null,
        public ?bool $weight = null,
    ) {}
}
