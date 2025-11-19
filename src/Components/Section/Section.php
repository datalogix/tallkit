<?php

namespace TALLKit\Components\Section;

use Illuminate\View\ComponentSlot;
use TALLKit\View\BladeComponent;

class Section extends BladeComponent
{
    public function __construct(
        public string|ComponentSlot|null $title = null,
        public string|ComponentSlot|null $subtitle = null,
        public ?bool $separator = null,
        public ?string $size = null,
    ) {}
}
