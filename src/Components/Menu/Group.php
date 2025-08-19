<?php

namespace TALLKit\Components\Menu;

use TALLKit\View\BladeComponent;

class Group extends BladeComponent
{
    public function __construct(
        public ?string $heading = null,
        public ?string $size = null,
    ) {}
}
