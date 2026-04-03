<?php

namespace TALLKit\Components\Skeleton;

use TALLKit\View\BladeComponent;

class Line extends BladeComponent
{
    public function __construct(
        public ?string $size = null,
    ) {}
}
