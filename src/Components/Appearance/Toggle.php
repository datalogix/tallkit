<?php

namespace TALLKit\Components\Appearance;

use TALLKit\View\BladeComponent;

class Toggle extends BladeComponent
{
    public function __construct(
        public null|bool|array $animate = null,
    ) {}
}
