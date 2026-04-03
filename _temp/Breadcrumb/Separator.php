<?php

namespace TALLKit\Components\Breadcrumb;

use Illuminate\View\ComponentSlot;
use TALLKit\View\BladeComponent;

class Separator extends BladeComponent
{
    public function __construct(
        public string|ComponentSlot|null $icon = null,
        public ?string $size = null,
    ) {}
}
