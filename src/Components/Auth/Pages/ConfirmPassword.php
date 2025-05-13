<?php

namespace TALLKit\Components\Auth\Pages;

use TALLKit\View\BladeComponent;

class ConfirmPassword extends BladeComponent
{
    public function render()
    {
        return <<<'BLADE'
            <div class="flex flex-col gap-6">
                <div class="flex w-full flex-col text-center space-y-2">
                    <tk:heading size="xl">{{ __('Log in to your account') }}</tk:heading>
                    <tk:text>{{ __('Enter your email and password below to log in') }}</tk:text>
                </div>

                <form wire:submit="confirmPassword" class="flex flex-col gap-6">
                    <!-- Password -->
                    <tk:input
                        wire:model="password"
                        :label="__('Password')"
                        type="password"
                        required
                        autocomplete="new-password"
                        :placeholder="__('Password')"
                    />

                    <tk:button variant="primary" type="submit" class="w-full">{{ __('Confirm') }}</tk:button>
                </form>
            </div>
        BLADE;
    }
}
