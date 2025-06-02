<?php

namespace TALLKit\Components\Avatar;

use TALLKit\View\BladeComponent;

class Group extends BladeComponent
{
    public function __construct(
        public ?string $size = null,
        public ?bool $square = null,
    ) {}
}
