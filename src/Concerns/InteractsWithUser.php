<?php

namespace TALLKit\Concerns;

use Illuminate\View\ComponentAttributeBag;

trait InteractsWithUser
{
    public function userAttributes(ComponentAttributeBag $attributes)
    {
        $guard = $attributes->pluck('guard');
        $user = $attributes->pluck('user', auth($guard)->user());
        $name = $attributes->pluck('name', data_get($user, 'name'));
        $email = $attributes->pluck('email', data_get($user, 'email'));
        $username = $attributes->pluck('username', data_get($user, 'username'));

        return [$user, $name, $email, $username];
    }
}
