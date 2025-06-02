<?php

namespace TALLKit\Components\Tooltip;

use TALLKit\View\BladeComponent;

class Wrapper extends BladeComponent
{
    public function __construct(
        public ?string $tooltip = null,
    ) {}
}
