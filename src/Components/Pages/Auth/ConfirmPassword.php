<?php

namespace TALLKit\Components\Pages\Auth;

use TALLKit\View\BladeComponent;

class ConfirmPassword extends BladeComponent
{
    public function __construct(
        public ?string $size = null,
    ) {}
}
