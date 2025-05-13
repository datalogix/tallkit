<?php

namespace TALLKit\Components\Auth\Pages;

use TALLKit\View\BladeComponent;

class Login extends BladeComponent
{
    public function render()
    {
        return <<<'BLADE'
            <div class="flex flex-col gap-6">
                <div class="flex w-full flex-col text-center space-y-2">
                    <tk:heading size="xl">{{ __('Log in to your account') }}</tk:heading>
                    <tk:text>{{ __('Enter your email and password below to log in') }}</tk:text>
                </div>

                <form wire:submit="login" class="flex flex-col gap-6">
                    <!-- Email Address -->
                    <tk:input
                        wire:model="email"
                        :label="__('Email address')"
                        type="email"
                        required
                        autofocus
                        autocomplete="email"
                        placeholder="email@example.com"
                    />

                    <!-- Password -->
                    <div class="relative">
                        <tk:input
                            wire:model="password"
                            :label="__('Password')"
                            type="password"
                            required
                            autocomplete="current-password"
                            :placeholder="__('Password')"
                        />

                        @if (Route::has('password.request'))
                            <flux:link class="absolute end-0 top-0 text-sm" :href="route('password.request')" wire:navigate>
                                {{ __('Forgot your password?') }}
                            </flux:link>
                        @endif
                    </div>

                    <!-- Remember Me -->
                    {{-- <tk:checkbox wire:model="remember" :label="__('Remember me')" /> --}}

                    <div class="flex items-center justify-end">
                        <tk:button variant="primary" type="submit" class="w-full">{{ __('Log in') }}</tk:button>
                    </div>
                </form>

                @if (Route::has('register'))
                    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
                        {{ __('Don\'t have an account?') }}
                        {{-- <tk:link :href="route('register')" wire:navigate>{{ __('Sign up') }}</tk:link> --}}
                    </div>
                @endif
            </div>
        BLADE;
    }
}
