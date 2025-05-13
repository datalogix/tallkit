<?php

namespace TALLKit\Components\Auth\Pages;

use TALLKit\View\BladeComponent;

class ResetPassword extends BladeComponent
{
    public function render()
    {
        return <<<'BLADE'
            <div class="flex flex-col gap-6">
                <div class="flex w-full flex-col text-center space-y-2">
                    <tk:heading size="xl">{{ __('Log in to your account') }}</tk:heading>
                    <tk:text>{{ __('Enter your email and password below to log in') }}</tk:text>
                </div>

                <form wire:submit="resetPassword" class="flex flex-col gap-6">
                    <!-- Email Address -->
                    <tk:input
                        wire:model="email"
                        :label="__('Email')"
                        type="email"
                        required
                        autocomplete="email"
                    />

                    <!-- Password -->
                    <tk:input
                        wire:model="password"
                        :label="__('Password')"
                        type="password"
                        required
                        autocomplete="new-password"
                        :placeholder="__('Password')"
                    />

                    <!-- Confirm Password -->
                    <tk:input
                        wire:model="password_confirmation"
                        :label="__('Confirm password')"
                        type="password"
                        required
                        autocomplete="new-password"
                        :placeholder="__('Confirm password')"
                    />

                    <div class="flex items-center justify-end">
                        <tk:button type="submit" variant="primary" class="w-full">
                            {{ __('Reset password') }}
                        </tk:button>
                    </div>
                </form>
            </div>
        BLADE;
    }
}
