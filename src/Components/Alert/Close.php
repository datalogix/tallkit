<?php

namespace TALLKit\Components\Alert;

use TALLKit\View\BladeComponent;

class Close extends BladeComponent
{
    public function __construct(
        public ?string $type = null,
        public ?string $icon = null,
    ) {}
}
