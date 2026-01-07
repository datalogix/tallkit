<?php

namespace TALLKit\Components\Label;

use Illuminate\View\ComponentSlot;
use TALLKit\Concerns\InteractsWithElement;
use TALLKit\View\BladeComponent;

class Label extends BladeComponent
{
    use InteractsWithElement;

    public function __construct(
        public ?string $as = null,
        public null|bool|string|ComponentSlot $label = null,
        public null|bool|string|ComponentSlot $labelAppend = null,
        public null|bool|string|ComponentSlot $labelPrepend = null,
        public ?string $for = null,
        public ?string $size = null,
        public ?bool $srOnly = null,
    ) {}
}
