<?php

namespace TALLKit\Components\Layouts\App;

use TALLKit\View\BladeComponent;

class Header extends BladeComponent
{
    public function render()
    {
        return <<<'BLADE'
        <div class="min-h-screen bg-white dark:bg-zinc-800">
            <tk:header container class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
                <tk:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

                <tk:brand href="#" logo="https://fluxui.dev/img/demo/logo.png" name="Acme Inc." class="max-lg:hidden dark:hidden" />
                <tk:brand href="#" logo="https://fluxui.dev/img/demo/dark-mode-logo.png" name="Acme Inc." class="max-lg:hidden! hidden dark:flex" />


                <tk:navbar class="-mb-px max-lg:hidden">
                    <tk:navbar.item icon="layout-grid" wire:navigate>
                        {{ __('Dashboard') }}
                    </tk:navbar.item>
                </tk:navbar>

                <tk:spacer />

                <tk:navbar class="me-1.5 space-x-0.5 rtl:space-x-reverse py-0!">
                    <tk:navbar.item class="!h-10 [&>div>svg]:size-5" icon="magnifying-glass" href="#" :label="__('Search')" />
                    <tk:navbar.item
                        class="h-10 max-lg:hidden [&>div>svg]:size-5"
                        icon="folder-git-2"
                        href="https://github.com/laravel/livewire-starter-kit"
                        target="_blank"
                        :label="__('Repository')"
                    />
                    <tk:navbar.item
                        class="h-10 max-lg:hidden [&>div>svg]:size-5"
                        icon="book-open-text"
                        href="https://laravel.com/docs/starter-kits"
                        target="_blank"
                        label="Documentation"
                    />
                </tk:navbar>

                <!-- Desktop User Menu -->
                <tk:dropdown position="top" align="end">
                    <tk:profile
                        class="cursor-pointer"
                        :initials="auth()->user()?->initials()"
                    />

                    <tk:menu>
                        <tk:menu.radio.group>
                            <div class="p-0 text-sm font-normal">
                                <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                    <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                        <span
                                            class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                        >
                                            {{ auth()->user()?->initials() }}
                                        </span>
                                    </span>

                                    <div class="grid flex-1 text-start text-sm leading-tight">
                                        <span class="truncate font-semibold">{{ auth()->user()?->name }}</span>
                                        <span class="truncate text-xs">{{ auth()->user()?->email }}</span>
                                    </div>
                                </div>
                            </div>
                        </tk:menu.radio.group>

                        <tk:menu.separator />

                        <tk:menu.radio.group>
                            <tk:menu.item  icon="cog" wire:navigate>{{ __('Settings') }}</tk:menu.item>
                        </tk:menu.radio.group>

                        <tk:menu.separator />

                        <form method="POST" action="" class="w-full">
                            @csrf
                            <tk:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                                {{ __('Log Out') }}
                            </tk:menu.item>
                        </form>
                    </tk:menu>
                </tk:dropdown>
            </tk:header>

            <tk:sidebar sticky stashable class="lg:hidden border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
                <tk:sidebar.toggle class="lg:hidden" icon="x-mark" />

                <tk:brand href="#" logo="https://fluxui.dev/img/demo/logo.png" name="Acme Inc." class="px-2 dark:hidden" />
                <tk:brand href="#" logo="https://fluxui.dev/img/demo/dark-mode-logo.png" name="Acme Inc." class="px-2 hidden dark:flex" />

                <tk:navlist variant="outline">
                    <tk:navlist.group :heading="__('Platform')">
                        <tk:navlist.item icon="layout-grid" wire:navigate>
                            {{ __('Dashboard') }}
                        </tk:navlist.item>
                    </tk:navlist.group>
                </tk:navlist>

                <tk:spacer />

                <tk:navlist variant="outline">
                    <tk:navlist.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                        {{ __('Repository') }}
                    </tk:navlist.item>

                    <tk:navlist.item icon="book-open-text" href="https://laravel.com/docs/starter-kits" target="_blank">
                        {{ __('Documentation') }}
                    </tk:navlist.item>
                </tk:navlist>
            </tk:sidebar>

            <tk:main>
                {{ $slot }}
            </tk:main>
        </div>
        BLADE;
    }
}
