<div {{ $attributes
    ->whereDoesntStartWith(['header:', 'area:', 'brand:', 'menu:', 'spacer:', 'appearance:', 'appearance-', 'user-menu:', 'sidebar:', 'sidebar-', 'main:'])
    ->classes('min-h-screen')
}}>
    <tk:sidebar
        :attributes="$attributesAfter('sidebar:')"
        sticky
        stashable
    >
        <tk:sidebar.toggle
            :attributes="$attributesAfter('sidebar-close:')->classes('lg:hidden')"
            icon="close"
        />

        <tk:brand
            :attributes="$attributesAfter('sidebar-brand:')"
            size="lg"
        >
            {{ $brand ?? '' }}
        </tk:brand>

        {{ $prepend ?? '' }}
        {{ $header ?? '' }}

        <tk:nav
            :attributes="$attributesAfter('sidebar-menu:')"
            :items="$menu"
            list
        >
            {{ $nav ?? '' }}
        </tk:nav>

        {{ $sidebar ?? '' }}
        {{ $append ?? '' }}
    </tk:sidebar>

    <tk:header
        :attributes="$attributesAfter('header:')->classes('gap-2')"
    >
        <tk:sidebar.toggle
            :attributes="$attributesAfter('sidebar-open:')->classes('lg:hidden')"
        />

        {{ $prepend ?? '' }}

        <tk:spacer :attributes="$attributesAfter('spacer:')" />

        {{ $append ?? '' }}
        {{ $search ?? '' }}

        @if ($appearance === 'toggle' || (($appearance === null || $appearance === true) && !($userMenu || isset($avatarMenu))))
            <tk:appearance.toggle
                :attributes="$attributesAfter('appearance:')"
            />
        @endif

        {{ $notification ?? '' }}

        @if ($userMenu || isset($avatarMenu))
            <tk:avatar.menu
                :attributes="$attributesAfter('user-menu:')"
                :items="$userMenu"
            >
                {{ $avatarMenu ?? '' }}

                @if ($appearance === 'selector' || $appearance === null || $appearance === true)
                    <x-slot:prepend>
                        <tk:appearance.menu-item
                            :attributes="$attributesAfter('appearance-menu-item:')"
                        />

                        <tk:menu.separator />
                    </x-slot:prepend>
                @endif
            </tk:avatar.menu>
        @endif
    </tk:header>

    <tk:main
        :attributes="$attributesAfter('main:')"
        container
    >
        {{ $slot }}
    </tk:main>
</div>
