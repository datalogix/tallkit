<?php

namespace TALLKit\Components\Autocomplete;

use TALLKit\Concerns\InteractsWithJsonOptions;
use TALLKit\View\BladeComponent;

class Autocomplete extends BladeComponent
{
    use InteractsWithJsonOptions;

    public function __construct(
        public mixed $items = null,
        public ?string $size = null,
    ) {}
}
