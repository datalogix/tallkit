<?php

namespace TALLKit\Components\Fieldset;

use Illuminate\View\ComponentSlot;
use TALLKit\View\BladeComponent;

class Fieldset extends BladeComponent
{
    public function __construct(
        public ?string $size = null,
        public null|bool|string|ComponentSlot $label = null,
        public null|bool|string|ComponentSlot $legend = null,
        public null|bool|string|ComponentSlot $description = null,
    ) {}
}
