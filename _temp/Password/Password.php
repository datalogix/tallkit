<?php

namespace TALLKit\Components\Password;

use TALLKit\Concerns\InteractsWithField;
use TALLKit\View\BladeComponent;

class Password extends BladeComponent
{
    use InteractsWithField;

    public function __construct(
        public bool $viewable = true,
    ) {}
}
