<?php

namespace TALLKit\Components\Auth\Pages;

use TALLKit\View\BladeComponent;

class Register extends BladeComponent
{
    public function render()
    {
        return <<<'BLADE'
            <div class="flex flex-col gap-6">
                <div class="flex w-full flex-col text-center space-y-2">
                    <tk:heading size="xl">{{ __('Log in to your account') }}</tk:heading>
                    <tk:text>{{ __('Enter your email and password below to log in') }}</tk:text>
                </div>

                <form wire:submit="register" class="flex flex-col gap-6">
                    <!-- Name -->
                    <tk:input
                        wire:model="name"
                        :label="__('Name')"
                        type="text"
                        required
                        autofocus
                        autocomplete="name"
                        :placeholder="__('Full name')"
                    />

                    <!-- Email Address -->
                    <tk:input
                        wire:model="email"
                        :label="__('Email address')"
                        type="email"
                        required
                        autocomplete="email"
                        placeholder="email@example.com"
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
                            {{ __('Create account') }}
                        </tk:button>
                    </div>
                </form>

                <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
                    {{ __('Already have an account?') }}
                    {{-- <tk:link :href="route('login')" wire:navigate>{{ __('Log in') }}</tk:link> --}}
                </div>
            </div>
        BLADE;
    }
}
