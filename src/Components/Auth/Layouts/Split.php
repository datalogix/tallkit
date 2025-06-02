<?php

namespace TALLKit\Components\Auth\Layouts;

use TALLKit\View\BladeComponent;

class Split extends BladeComponent
{
    public function __construct(
        public string $position = 'right',
    ) {
    }
}
