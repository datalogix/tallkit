<?php

namespace TALLKit\Components\Fetchable;

use TALLKit\Concerns\InteractsWithJsonOptions;
use TALLKit\View\BladeComponent;

class Fetchable extends BladeComponent
{
    use InteractsWithJsonOptions;

    public function __construct(
        public ?string $url = null,
        public $data = null,
        public ?bool $autofetch = null,
        public ?string $chart = null,
    ) {}
}
