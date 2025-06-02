<?php

namespace TALLKit\Components\Field;

use TALLKit\View\BladeComponent;

class Field extends BladeComponent
{
    public function __construct(
        public ?string $name = null,
        public ?string $id = null,
        public ?string $label = null,
        public ?string $description = null,
        public ?string $help = null,
    ) {
    }
}
