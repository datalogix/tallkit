<?php

namespace TALLKit\Components\Container;

use TALLKit\View\BladeComponent;

class Wrapper extends BladeComponent
{
    public function __construct(
        public ?bool $container = null,
    ) {}
}
