<?php

namespace TALLKit\Components\Error;

use TALLKit\Concerns\InteractsWithErrorBag;
use TALLKit\View\BladeComponent;

class Error extends BladeComponent
{
    use InteractsWithErrorBag;

    public function __construct(
        public ?string $name = null,
        public ?string $size = null,
        public ?string $message = null,
        public null|bool|string $icon = null,
    ) {}
}
