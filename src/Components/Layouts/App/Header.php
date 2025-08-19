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
                <tk:sidebar.toggle class="lg:hidden" icon="bars-2" />

                <tk:brand href="#" logo="https://fluxui.dev/img/demo/logo.png" name="Acme Inc." class="max-lg:hidden dark:hidden me-4" />
                <tk:brand href="#" logo="https://fluxui.dev/img/demo/dark-mode-logo.png" name="Acme Inc." class="max-lg:hidden! hidden dark:flex me-4" />

                <tk:nav class="-mb-px max-lg:hidden">
                    <tk:nav.item icon="layout-grid" wire:navigate>
                        {{ __('Dashboard') }}
                    </tk:nav.item>
                </tk:nav>

                <tk:spacer />

                <tk:nav class="me-1.5 ms-1.5 space-x-0.5 rtl:space-x-reverse py-0!">
                    <tk:nav.item icon="magnifying-glass" href="#" :label="__('Search')" />
                    <tk:nav.item
                        icon="lucide:folder-git-2"
                        href="https://github.com/laravel/livewire-starter-kit"
                        target="_blank"
                        :label="__('Repository')"
                    />
                    <tk:nav.item
                        icon="book-open-text"
                        href="https://laravel.com/docs/starter-kits"
                        target="_blank"
                        label="Documentation"
                    />
                </tk:nav>

                <tk:appearance.toggle class="ms-4 me-4" variant="none" />

                <!-- Desktop User Menu -->
                <tk:dropdown position="top" align="end">
                    <tk:avatar.profile
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
                                            {{ auth()->user()?->initials() ?? 'R' }}
                                        </span>
                                    </span>

                                    <div class="grid flex-1 text-start text-sm leading-tight">
                                        <span class="truncate font-semibold">{{ auth()->user()?->name ?? 'Ricardo' }}</span>
                                        <span class="truncate text-xs">{{ auth()->user()?->email ?? 'ricardo@datalogix.com.br' }}</span>
                                    </div>
                                </div>
                            </div>
                        </tk:menu.radio.group>

                        <tk:menu.separator />

                        <tk:menu.radio.group>
                            <tk:menu.item icon="cog" wire:navigate>{{ __('Settings') }}</tk:menu.item>
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

                <tk:nav list>
                    <tk:nav.group :heading="__('Platform')">
                        <tk:nav.item icon="layout-grid" wire:navigate>
                            {{ __('Dashboard') }}
                        </tk:nav.item>
                    </tk:nav.group>
                </tk:nav>

                <tk:spacer />

                <tk:nav list>
                    <tk:nav.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                        {{ __('Repository') }}
                    </tk:nav.item>

                    <tk:nav.item icon="book-open-text" href="https://laravel.com/docs/starter-kits" target="_blank">
                        {{ __('Documentation') }}
                    </tk:nav.item>
                </tk:nav>
            </tk:sidebar>


            <tk:main container>
                <div class="flex max-md:flex-col items-start">
                    <div class="w-full md:w-[220px] pb-4 me-10">
                        <tk:nav list>
                            <tk:nav.item href="#" current>Dashboard</tk:nav.item>
                            <tk:nav.item href="#" badge="32">Orders</tk:nav.item>
                            <tk:nav.item href="#">Catalog</tk:nav.item>
                            <tk:nav.item href="#">Payments</tk:nav.item>
                            <tk:nav.item href="#">Customers</tk:nav.item>
                            <tk:nav.item href="#">Billing</tk:nav.item>
                            <tk:nav.item href="#">Quotes</tk:nav.item>
                            <tk:nav.item href="#">Configuration</tk:nav.item>
                        </tk:nav>
                    </div>
                    <tk:separator class="md:hidden" />
                    <div class="flex-1 max-md:pt-6 self-stretch">
                          {{ $slot }}
                            <tk:heading size="xl" level="1">Good afternoon, Olivia</tk:heading>
                            <tk:text class="mb-6 mt-2 text-base">Here's what's new today</tk:text>
                            <tk:separator variant="subtle" />
                    </div>
                </div>
            </tk:main>
        BLADE;
    }
}
