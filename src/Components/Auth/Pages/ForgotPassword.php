<?php

namespace TALLKit\Components\Auth\Pages;

use TALLKit\Attributes\Mount;
use TALLKit\View\BladeComponent;

class ForgotPassword extends BladeComponent
{
    public function __construct(
        public ?string $login = null,
    ) {}

    #[Mount()]
    protected function mount()
    {
        $this->login ??= route_detect([
            'login', 'auth.login',
            'signin', 'auth.signin',
            'sign-in', 'auth.sign-in',
        ], default: null);
    }
}
