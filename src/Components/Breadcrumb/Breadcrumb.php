<?php

namespace TALLKit\Components\Breadcrumb;

use TALLKit\View\BladeComponent;

class Breadcrumb extends BladeComponent
{
    public function __construct(
        public ?string $size = null,
        public mixed $items = null,
    ) {}
}
