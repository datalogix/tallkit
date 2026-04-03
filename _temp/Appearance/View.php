<?php

namespace TALLKit\Components\Appearance;

use TALLKit\View\BladeComponent;

class View extends BladeComponent
{
    public function __construct(
        public ?string $livewire = null,
    ) {}
}
