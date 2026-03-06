<?php

namespace TALLKit\Components\Breadcrumb;

use TALLKit\View\BladeComponent;

class Breadcrumb extends BladeComponent
{
    public function __construct(
        public mixed $items = null,
        public ?string $size = null,
        public ?string $mode = null,
        public ?string $variant = null,
        public ?string $contrast = null,
    ) {}
}
