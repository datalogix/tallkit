<?php

namespace TALLKit\Components\Loading;

use TALLKit\View\BladeComponent;

class Loading extends BladeComponent
{
    public function __construct(
        public ?string $variant = null,
        public ?string $size = null,
    ) {}
}
