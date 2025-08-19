<?php

namespace TALLKit\Components\Footer;

use TALLKit\View\BladeComponent;

class Footer extends BladeComponent
{
    public function __construct(
        public ?bool $container = null,
    ) {}
}
