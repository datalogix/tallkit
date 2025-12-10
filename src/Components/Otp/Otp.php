<?php

namespace TALLKit\Components\Otp;

use TALLKit\Concerns\InteractsWithField;
use TALLKit\View\BladeComponent;

class Otp extends BladeComponent
{
    use InteractsWithField;

    public function __construct(
        public ?int $length = null,
        public ?bool $private = null,
        public ?string $mode = null,
        public ?string $submit = null,
    ) {}
}
