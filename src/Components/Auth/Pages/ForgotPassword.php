<?php

namespace TALLKit\Components\Auth\Pages;

use TALLKit\View\BladeComponent;

class ForgotPassword extends BladeComponent
{
    public function render()
    {
        return <<<'BLADE'
            <div class="flex flex-col gap-6">
                <div class="flex w-full flex-col text-center space-y-2">
                    <tk:heading size="xl">{{ __('Log in to your account') }}</tk:heading>
                    <tk:text>{{ __('Enter your email and password below to log in') }}</tk:text>
                </div>

                <form wire:submit="sendPasswordResetLink" class="flex flex-col gap-6">
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

                    <tk:button variant="primary" type="submit" class="w-full">{{ __('Email password reset link') }}</tk:button>
                </form>

                <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-400">
                    {{ __('Or, return to') }}

                    {{-- <tk:link :href="route('login')" wire:navigate>{{ __('log in') }}</tk:link> --}}
                </div>
            </div>
        BLADE;
    }
}
