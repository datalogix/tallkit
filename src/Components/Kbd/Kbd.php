<?php

namespace TALLKit\Components\Kbd;

use Illuminate\View\ComponentSlot;
use TALLKit\Concerns\InteractsWithElement;
use TALLKit\View\BladeComponent;

class Kbd extends BladeComponent
{
    use InteractsWithElement;

    public function __construct(
        public bool|string|ComponentSlot|null $label = null,
        public ?string $variant = null,
    ) {}
}
