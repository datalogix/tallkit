<?php

namespace TALLKit\Components\Link;

use TALLKit\View\BladeComponent;

class Link extends BladeComponent
{
    public function __construct(
        public ?string $variant = null,
        public ?string $size = null,
        public ?bool $underline = null,
    ) {}
}
