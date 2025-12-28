<?php

namespace TALLKit\Components\PrettyPrintJson;

use TALLKit\Concerns\InteractsWithJsonOptions;
use TALLKit\View\BladeComponent;

class PrettyPrintJson extends BladeComponent
{
    use InteractsWithJsonOptions;

    public function __construct(
        public ?array $data = null,
    ) {}
}
