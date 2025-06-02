<?php

namespace TALLKit\Components\Badge;

use TALLKit\View\BladeComponent;

class Close extends BladeComponent
{
    public function __construct(
        public ?string $icon = null,
    ) {}
}
