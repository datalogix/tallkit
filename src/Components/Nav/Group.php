<?php

namespace TALLKit\Components\Nav;

use TALLKit\View\BladeComponent;

class Group extends BladeComponent
{
    public function __construct(
        public ?bool $expanded = null,
        public ?bool $expandable = null,
        public ?string $heading = null,
        public ?bool $line = null,
        public bool|string|null $collapse = null,
    ) {}
}
