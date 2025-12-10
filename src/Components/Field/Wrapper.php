<?php

namespace TALLKit\Components\Field;

use Illuminate\View\ComponentSlot;
use TALLKit\View\BladeComponent;

class Wrapper extends BladeComponent
{
    public function __construct(
        public ?string $variant = null,
        public ?string $align = null,
        public ?string $name = null,
        public ?string $id = null,
        public null|bool|string|ComponentSlot $label = null,
        public null|bool|string|ComponentSlot $labelAppend = null,
        public null|bool|string|ComponentSlot $labelPrepend = null,
        public null|bool|string|ComponentSlot $description = null,
        public null|bool|string|ComponentSlot $help = null,
        public null|bool|string|ComponentSlot $badge = null,
        public null|bool|string|ComponentSlot $info = null,
        public null|bool|string|ComponentSlot $prefix = null,
        public null|bool|string|ComponentSlot $suffix = null,
        public ?string $size = null,
        public ?bool $showError = null,
    ) {}
}
