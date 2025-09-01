<?php

namespace TALLKit\Components\Layouts\App;

use TALLKit\View\BladeComponent;

class Sidebar extends BladeComponent
{
    public function render()
    {
        return <<<'BLADE'
        <div class="min-h-screen">
            <tk:sidebar sticky stashable class="bg-zinc-50 dark:bg-zinc-800">
                <tk:sidebar.toggle class="lg:hidden" icon="x-mark" />
                <tk:brand />
                <tk:appearance.toggle />
                <tk:nav list>
                    <tk:nav.group :heading="__('Platform')" class="grid">
                        <tk:nav.item icon="home" wire:navigate>
                            {{ __('Dashboard') }}
                        </tk:nav.item>
                    </tk:nav.group>
                </tk:nav>
                <tk:spacer />
                <tk:nav
                    list
                    :items="[
                        ['label' => 'Repository', 'icon' => 'folder-git-2'],
                        ['label' => 'Documentation', 'icon' => 'book-open-text'],
                    ]"
                />
                <tk:avatar.menu
                    profile
                    :items="[['route' => 'auth.logout', 'label' => 'Logout', 'icon' => 'logout']]"
                />
            </tk:sidebar>

            <tk:header class="lg:hidden">
                <tk:sidebar.toggle class="lg:hidden" />
                <tk:spacer />
                <tk:appearance.toggle />
                <tk:avatar.menu :items="[['route' => 'auth.logout', 'label' => 'Logout', 'icon' => 'logout']]" />
            </tk:header>

            <tk:main>
                <tk:card>
                {{ $slot }}
                </tk:card>
            </tk:main>
        </div>
        BLADE;
    }
}
