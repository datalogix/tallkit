<?php

namespace TALLKit\Components\Page;

use Illuminate\View\ComponentSlot;
use TALLKit\View\BladeComponent;

class Page extends BladeComponent
{
    public function __construct(
        public ?string $breakpoint = null,
        public mixed $menu = null,
        public ?ComponentSlot $actions = null,
    ) {}
}
