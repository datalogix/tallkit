<?php

namespace TALLKit\Components\Text;

use TALLKit\View\BladeComponent;

class Text extends BladeComponent
{
    public function __construct(
        public ?string $text = null,
        public ?bool $inline = null,
        public ?string $variant = null,
        public ?string $color = null,
        public ?string $size = null,
    ) {}
}
