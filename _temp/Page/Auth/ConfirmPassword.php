<?php

namespace TALLKit\Components\Page\Auth;

use TALLKit\View\BladeComponent;

class ConfirmPassword extends BladeComponent
{
    public function __construct(
        public ?string $size = null,
    ) {}
}
