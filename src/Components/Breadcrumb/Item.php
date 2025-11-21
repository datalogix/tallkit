<?php

namespace TALLKit\Components\Breadcrumb;

use Illuminate\View\ComponentSlot;
use TALLKit\Concerns\InteractsWithElement;
use TALLKit\View\BladeComponent;

class Item extends BladeComponent
{
    use InteractsWithElement;

    public function __construct(
        public ?string $href = null,
        public string|ComponentSlot|null $separator = null,
    ) {}
}
