<?php

namespace TALLKit\Components\Avatar;

use TALLKit\View\BladeComponent;

class Menu extends BladeComponent
{
    public function __construct(
        public ?bool $profile = null,
        public mixed $items = null,
        public ?string $size = null,
    ) {}
}
