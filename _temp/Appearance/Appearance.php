<?php

namespace TALLKit\Components\Appearance;

use TALLKit\View\BladeComponent;

class Appearance extends BladeComponent
{
    public function __construct(
        public ?string $nonce = null,
    ) {}
}
