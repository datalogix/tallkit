<?php

namespace TALLKit\Components\Table\Column;

use TALLKit\View\BladeComponent;

class Column extends BladeComponent
{
    public function __construct(
        public ?string $name = null,
        public ?string $label = null,
        public null|bool|string $sortable = null,
        public ?string $align = null,
        public ?bool $sticky = null,
    ) {}
}
