<?php

namespace TALLKit\Components\Textarea;

use TALLKit\Concerns\InteractsWithField;
use TALLKit\View\BladeComponent;

class Textarea extends BladeComponent
{
    use InteractsWithField;

    public function __construct(
        public ?string $resize = null,
        public null|string|int $rows = null,
    ) {}
}
