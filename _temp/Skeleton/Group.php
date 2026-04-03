<?php

namespace TALLKit\Components\Skeleton;

use TALLKit\View\BladeComponent;

class Group extends BladeComponent
{
    public function __construct(
        public ?string $animate = null,
    ) {}
}
