<?php

namespace TALLKit\Components\Menu;

use Illuminate\View\ComponentSlot;
use TALLKit\View\BladeComponent;

class Submenu extends BladeComponent
{
    public function __construct(
        public ?string $heading = null,
        public string|ComponentSlot|null $icon = null,
        public string|ComponentSlot|null $iconTrailing = null,
    ) {}
}
