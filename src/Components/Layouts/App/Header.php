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
                <tk:sidebar.toggle class="lg:hidden" />
                <tk:brand class="max-lg:hidden me-4" />
                <tk:nav :items="[['label' => 'Dashboard', 'icon' => 'layout-grid']]" />
                <tk:spacer />
                <tk:nav :items="[['route' => 'auth.logout', 'label' => 'Logout', 'icon' => 'logout']]" />
                <tk:appearance.toggle />
                <tk:avatar.menu :items="[['route' => 'auth.logout', 'label' => 'Logout', 'icon' => 'logout']]" />
            </tk:header>

            <tk:sidebar sticky stashable class="lg:hidden bg-zinc-50 dark:bg-zinc-900">
                <tk:sidebar.toggle class="lg:hidden" icon="x-mark" />
                <tk:brand />
                <tk:nav list>
                    <tk:nav.group :heading="__('Platform')">
                        <tk:nav.item icon="layout-grid" wire:navigate>
                            {{ __('Dashboard') }}
                        </tk:nav.item>
                    </tk:nav.group>
                </tk:nav>
                <tk:spacer />
                <tk:nav list
                    :items="[
                        ['label' => 'Repository', 'icon' => 'folder-git-2'],
                        ['label' => 'Documentation', 'icon' => 'book-open-text'],
                    ]"
                />
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
                        <tk:heading size="xl" level="1">Good afternoon, Olivia</tk:heading>
                        <tk:text class="mb-6 mt-2 text-base">Here's what's new today</tk:text>
                        <tk:separator variant="subtle" />
                    </div>
                </div>
            </tk:main>
        </div>
        BLADE;
    }
}
