<?php

namespace TALLKit\Components\Avatar;

use TALLKit\View\BladeComponent;

class Group extends BladeComponent
{
    public function __construct(
        public mixed $avatars = null,
        public ?int $max = null,
    ) {}
}
