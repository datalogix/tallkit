<?php

namespace TALLKit\Components\Auth\Layouts;

use TALLKit\View\BladeComponent;

class Simple extends BladeComponent
{
    public function __construct() {}

    public function render()
    {
        return <<<'BLADE'
            <div {{ $attributes }} class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
                <div class="bg-background flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
                    <div class="flex w-full max-w-sm flex-col gap-2">
                        <a href="" class="flex flex-col items-center gap-3 font-medium" wire:navigate>
                            <span class="flex h-9 w-9 items-center justify-center rounded-md">
                                Logo
                            </span>
                            <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                        </a>
                        <div class="flex-1">
                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </div>
        BLADE;
    }
}
