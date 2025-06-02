<?php

namespace TALLKit\Components\Loading;

use TALLKit\View\BladeComponent;

class Loading extends BladeComponent
{
    public function __construct(
        public ?string $type = null,
        public ?string $size = null,
    ) {}
}
