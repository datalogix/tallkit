<?php

namespace TALLKit\Components\Header;

use TALLKit\View\BladeComponent;

class Header extends BladeComponent
{
    public function __construct(
        public ?bool $sticky = null,
        public ?bool $container = null,
        public ?string $variant = null,
    ) {}
}
