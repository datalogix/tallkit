<?php

namespace TALLKit\Components\Slider;

use TALLKit\Concerns\InteractsWithField;
use TALLKit\View\BladeComponent;

class Slider extends BladeComponent
{
    use InteractsWithField;

    public function __construct(
        public mixed $ticks = null,
    ) {}
}
