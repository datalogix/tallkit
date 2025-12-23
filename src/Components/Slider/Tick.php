<?php

namespace TALLKit\Components\Slider;

use Illuminate\View\ComponentSlot;
use TALLKit\View\BladeComponent;

class Tick extends BladeComponent
{
    public function __construct(
        public null|bool|string|ComponentSlot $label = null,
        public ?string $size = null,
    ) {}
}
