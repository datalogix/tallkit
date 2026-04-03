<?php

namespace TALLKit\Components\Heading;

use TALLKit\Concerns\InteractsWithElement;
use TALLKit\View\BladeComponent;

class Heading extends BladeComponent
{
    use InteractsWithElement;

    public function __construct(
        public ?string $size = null,
        public ?string $mode = null,
        public ?string $variant = null,
        public ?string $contrast = null,
    ) {}
}
