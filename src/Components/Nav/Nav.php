<?php

namespace TALLKit\Components\Nav;

use TALLKit\View\BladeComponent;

class Nav extends BladeComponent
{
    public function __construct(
        public ?bool $list = null,
        public ?string $variant = null,
        public ?string $size = null,
        public ?bool $scrollable = null,
    ) {}
}
