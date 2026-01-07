<?php

namespace TALLKit\Components\Autocomplete;

use TALLKit\View\BladeComponent;

class Autocomplete extends BladeComponent
{
    public function __construct(
        public mixed $items = null,
    ) {}
}
