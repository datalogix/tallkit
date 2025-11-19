<div {{ $attributes
    ->whereDoesntStartWith(['header:', 'brand:', 'menu:', 'appearance:', 'user-menu:', 'sidebar:', 'sidebar-', 'main:'])
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

        <tk:nav
            :attributes="$attributesAfter('sidebar-menu:')"
            :items="$menu"
            list
        >
            {{ $nav ?? '' }}
        </tk:nav>

        {{ $sidebar ?? '' }}

        <tk:spacer />

        @if ($appearance !== false)
            <tk:appearance.selector
                :attributes="$attributesAfter('sidebar-appearance:')->classes('justify-center gap-1')"
                variant="subtle"
                size="sm"
            />
        @endif

        @if ($userMenu || isset($avatarMenu))
            <tk:avatar.menu
                :attributes="$attributesAfter('sidebar-user-menu:')"
                :items="$userMenu"
                profile
            >
                {{ $avatarMenu ?? '' }}
            </tk:avatar.menu>
        @endif
    </tk:sidebar>

    <tk:header
        :attributes="$attributesAfter('header:')->classes('lg:hidden gap-2')"
    >
        <tk:sidebar.toggle
            :attributes="$attributesAfter('sidebar-open:')->classes('lg:hidden')"
        />

        {{ $header ?? '' }}

        <tk:spacer />

        @if ($appearance !== false)
            <tk:appearance.toggle
                :attributes="$attributesAfter('appearance:')"
            />
        @endif

        @if ($userMenu || isset($avatarMenu))
            <tk:avatar.menu
                :attributes="$attributesAfter('user-menu:')"
                :items="$userMenu"
            >
                {{ $avatarMenu ?? '' }}
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
