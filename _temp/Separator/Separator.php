<?php

namespace TALLKit\Components\Separator;

use TALLKit\View\BladeComponent;

class Separator extends BladeComponent
{
    public function __construct(
        public ?bool $vertical = null,
        public ?string $label = null,
    ) {}
}
