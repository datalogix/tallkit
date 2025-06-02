<div class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
    <div class="relative grid h-dvh items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
        <div {{ $attributesAfter('brand:')
            ->classes('bg-muted relative hidden h-full flex-col p-10 text-white lg:flex dark:border-e dark:border-neutral-800 bg-neutral-900')
            ->classes($position === 'right' ? 'order-first' : 'order-last')
        }}>
            @isset($brand)
                {{ $brand }}
            @else
                <a href="" class="z-20 flex items-center text-lg font-medium" wire:navigate>
                    <span class="flex h-10 w-10 items-center justify-center rounded-md">
                        Logo
                    </span>

                    {{ config('app.name', 'Laravel') }}
                </a>
                <div class="z-20 mt-auto">
                    <blockquote class="space-y-2">
                        dsa
                    </blockquote>
                </div>
            @endif
        </div>
        <div class="w-full lg:p-8">
            <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-sm">
                <a href="" class="z-20 flex flex-col items-center gap-3 font-medium lg:hidden" wire:navigate>
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
</div>
