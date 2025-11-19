<div {{ $attributes
    ->whereDoesntStartWith(['header:', 'brand:', 'menu:', 'appearance:', 'user-menu:', 'sidebar:', 'sidebar-', 'main:'])
    ->classes('min-h-screen')
}}>
    <tk:header
        :attributes="$attributesAfter('header:')->classes('gap-2')"
    >
        <tk:sidebar.toggle
            :attributes="$attributesAfter('sidebar-open:')->classes('lg:hidden')"
        />

        <tk:brand
            :attributes="$attributesAfter('brand:')->classes('max-lg:hidden me-4')"
        >
            {{ $brand ?? '' }}
        </tk:brand>

        <tk:spacer />

        <tk:nav
            :attributes="$attributesAfter('menu:')"
            :items="$menu"
        >
            {{ $nav ?? '' }}
        </tk:nav>

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

    <tk:sidebar
        :attributes="$attributesAfter('sidebar:')->classes('lg:hidden')"
        sticky
        stashable
    >
        <tk:sidebar.toggle
            :attributes="$attributesAfter('sidebar-close:')->classes('lg:hidden')"
            icon="close"
        />

        <tk:brand
            :attributes="$attributesAfter('sidebar-brand:')"
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
    </tk:sidebar>

    <tk:main
        :attributes="$attributesAfter('main:')"
        container
    >
        {{ $slot }}
    </tk:main>
</div>
