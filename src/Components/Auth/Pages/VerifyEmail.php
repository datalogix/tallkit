<?php

namespace TALLKit\Components\Auth\Pages;

use TALLKit\Attributes\Mount;
use TALLKit\View\BladeComponent;

class VerifyEmail extends BladeComponent
{
    public function __construct(
        public ?string $logout = null,
    ) {}

    #[Mount()]
    protected function mount()
    {
        $this->logout ??= route_detect(['logout', 'auth.logout'], default: null);
    }
}
