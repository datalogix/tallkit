<?php

namespace TALLKit\Components\Fieldset;

use Illuminate\View\ComponentSlot;
use TALLKit\View\BladeComponent;

class Fieldset extends BladeComponent
{
    public function __construct(
        public null|bool|string|ComponentSlot $label = null,
        public null|bool|string|ComponentSlot $legend = null,
        public null|bool|string|ComponentSlot $description = null,
        public null|bool|string|ComponentSlot $badge = null,
        public null|bool|string|ComponentSlot $info = null,
        public ?string $size = null,
    ) {}
}
