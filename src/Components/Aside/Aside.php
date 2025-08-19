<?php

namespace TALLKit\Components\Aside;

use TALLKit\View\BladeComponent;

class Aside extends BladeComponent
{
    public function __construct(
        public ?bool $sticky = null,
    ) {}
}
