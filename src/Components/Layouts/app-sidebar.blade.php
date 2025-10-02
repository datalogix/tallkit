<div {{ $attributes
    ->whereDoesntStartWith(['header:', 'brand:', 'menu:', 'appearance:', 'user-menu:', 'sidebar:', 'sidebar-', 'main:'])
    ->classes('min-h-screen')
}}>
    <tk:sidebar sticky stashable :attributes="$attributesAfter('sidebar:')">
        <tk:sidebar.toggle
            :attributes="$attributesAfter('sidebar-close:')->classes('lg:hidden')"
            icon="x-mark"
        />

        <tk:brand
            :attributes="$attributesAfter('sidebar-brand:')"
            size="lg"
        />

        <tk:nav
            :attributes="$attributesAfter('sidebar-menu:')"
            :items="$menu"
            list
        />

        {{ $sidebar ?? '' }}

        <tk:spacer />

        @if ($appearance !== false)
            <tk:appearance.group
                :attributes="$attributesAfter('sidebar-appearance:')->classes('justify-center gap-1')"
                variant="subtle"
                size="sm"
            />
        @endif

        <tk:avatar.menu
            :attributes="$attributesAfter('sidebar-user-menu:')"
            :items="$userMenu"
            profile
        />
    </tk:sidebar>

    <tk:header :attributes="$attributesAfter('header:')->classes('lg:hidden gap-2')">
        <tk:sidebar.toggle :attributes="$attributesAfter('sidebar-open:')->classes('lg:hidden')" />

        {{ $header ?? '' }}

        <tk:spacer />

        @if ($appearance !== false)
            <tk:appearance.toggle :attributes="$attributesAfter('appearance:')" />
        @endif

        <tk:avatar.menu
            :attributes="$attributesAfter('user-menu:')"
            :items="$userMenu"
        />
    </tk:header>

    <tk:main container :attributes="$attributesAfter('main:')">
        {{ $slot }}
    </tk:main>
</div>
