<?php

namespace TALLKit\Components\Command;

use Illuminate\View\ComponentSlot;
use TALLKit\Concerns\InteractsWithField;
use TALLKit\View\BladeComponent;

class Input extends BladeComponent
{
    use InteractsWithField;

    public function __construct(
        public ?bool $clearable = null,
        public string|ComponentSlot|null $icon = null,
    ) {}
}
