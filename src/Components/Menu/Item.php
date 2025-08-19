<?php

namespace TALLKit\Components\Menu;

use TALLKit\Concerns\InteractsWithElement;
use TALLKit\View\BladeComponent;

class Item extends BladeComponent
{
    use InteractsWithElement;

    public function __construct(
        public ?string $variant = null,
        public ?string $size = null,
        public ?bool $keepOpen = null,
    ) {}
}
