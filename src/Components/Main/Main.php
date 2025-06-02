<?php

namespace TALLKit\Components\Main;

use TALLKit\View\BladeComponent;

class Main extends BladeComponent
{
    public function __construct(
        public ?bool $container = null
    ) {}
}
