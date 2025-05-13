<?php

namespace TALLKit\Components\Auth\Pages;

use TALLKit\View\BladeComponent;

class VerifyEmail extends BladeComponent
{
    public function render()
    {
        return <<<'BLADE'
            <div class="flex flex-col gap-6">
                <tk:text class="text-center">
                    {{ __('Please verify your email address by clicking on the link we just emailed to you.') }}
                </tk:text>

                @if (session('status') == 'verification-link-sent')
                    <tk:text class="text-center font-medium !dark:text-green-400 !text-green-600">
                        {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                    </tk:text>
                @endif

                <div class="flex flex-col items-center justify-between space-y-3">
                    <tk:button wire:click="sendVerification" variant="primary" class="w-full">
                        {{ __('Resend verification email') }}
                    </tk:button>

                    {{-- <tk:link class="text-sm cursor-pointer" wire:click="logout">
                        {{ __('Log out') }}
                    </tk:link>--}}
                </div>
            </div>
        BLADE;
    }
}
