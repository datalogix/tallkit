<?php

namespace TALLKit\Components\Text;

use TALLKit\View\BladeComponent;

class Text extends BladeComponent
{
    public function __construct(
        public ?string $size = null,
        public ?string $mode = null,
        public ?bool $weight = null,
        public ?string $variant = null,
    ) {}
}
