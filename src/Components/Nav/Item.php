<?php

namespace TALLKit\Components\Nav;

use TALLKit\Concerns\InteractsWithElement;
use TALLKit\View\BladeComponent;

class Item extends BladeComponent
{
    use InteractsWithElement;

    public function __construct(
        public ?bool $square = null,
    ) {}
}
