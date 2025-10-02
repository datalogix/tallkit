<?php

namespace TALLKit\Components\Link;

use TALLKit\View\BladeComponent;

class Link extends BladeComponent
{
    public function __construct(
        public ?bool $underline = null,
    ) {}
}
