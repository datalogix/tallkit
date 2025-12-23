<?php

namespace TALLKit\Components\Menu\Checkbox;

use TALLKit\Concerns\InteractsWithElement;
use TALLKit\View\BladeComponent;

class Checkbox extends BladeComponent
{
    use InteractsWithElement;

    public function __construct(
        public ?bool $checked = null,
        public ?bool $keepOpen = null,
    ) {}
}
