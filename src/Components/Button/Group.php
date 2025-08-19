<?php

namespace TALLKit\Components\Button;

use TALLKit\View\BladeComponent;

class Group extends BladeComponent
{
    public function __construct(
        public ?string $variant = null,
        public ?string $size = null,
        public ?bool $circle = null,
        public ?bool $square = null,
    ) {}
}
