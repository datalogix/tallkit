<?php

namespace TALLKit\Components\Menu\Radio;

use TALLKit\View\BladeComponent;

class Group extends BladeComponent
{
    public function __construct(
        public ?bool $keepOpen = null,
        public mixed $items = null,
    ) {}
}
