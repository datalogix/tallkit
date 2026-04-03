<?php

namespace TALLKit\Components\Loadable;

use TALLKit\View\BladeComponent;

class Loadable extends BladeComponent
{
    public function __construct(
        public ?bool $silent = null,
    ) {}
}
