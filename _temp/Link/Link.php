<?php

namespace TALLKit\Components\Link;

use TALLKit\Concerns\InteractsWithElement;
use TALLKit\View\BladeComponent;

class Link extends BladeComponent
{
    use InteractsWithElement;

    public function __construct(
        public ?bool $underline = null,
    ) {}
}
