<?php

namespace TALLKit\Components\Tooltip;

use Illuminate\View\ComponentSlot;
use TALLKit\View\BladeComponent;

class Wrapper extends BladeComponent
{
    public function __construct(
        public string|ComponentSlot|null $tooltip = null,
    ) {}
}
