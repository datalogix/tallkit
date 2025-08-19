<?php

namespace TALLKit\Components\Auth\Pages;

use TALLKit\Attributes\Mount;
use TALLKit\View\BladeComponent;

class Login extends BladeComponent
{
    public function __construct(
        public ?string $forgotPassword = null,
        public ?string $signUp = null,
    ) {}

    #[Mount()]
    protected function mount()
    {
        $this->forgotPassword ??= route_detect(['forgot-password', 'auth.forgot-password'], default: null);
        $this->signUp ??= route_detect([
            'signup', 'auth.signup',
            'sign-up', 'auth.sign-up',
            'register', 'auth.register',
        ], default: null);
    }
}
