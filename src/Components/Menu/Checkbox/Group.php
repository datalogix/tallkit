<?php

namespace TALLKit\Components\Menu\Checkbox;

use TALLKit\View\BladeComponent;

class Group extends BladeComponent
{
    public function __construct(
        public ?bool $keepOpen = null,
    ) {}
}
