@props([
    'appearance' => null,
    'menu' => null,
    'userMenu' => null,
])
<div
    {{
        $attributes
            ->whereDoesntStartWith([
                'header:', 'area:', 'brand:', 'menu:', 'spacer:',
                'appearance:', 'sidebar', 'main:', 'aside:',
            ])
            ->classes('min-h-screen')
    }}
>
    <tk:sidebar
        :attributes="$attributes->prefixed('sidebar:')"
        sticky
        stashable
    >
        <tk:sidebar.toggle
            :attributes="$attributes->prefixed('sidebar-close:')->classes('lg:hidden')"
            icon="close"
        />

        <tk:brand
            :attributes="$attributes->prefixed('sidebar-brand:')"
            size="lg"
        >
            {{ $brand ?? '' }}
        </tk:brand>

        {{ $header ?? '' }}

        <tk:nav
            :attributes="$attributes->prefixed('sidebar-menu:')->merge(['label' => 'Main menu'])"
            :items="$menu"
            list
        >
            {{ $nav ?? '' }}
        </tk:nav>

        {{ $sidebar ?? '' }}
    </tk:sidebar>

    <tk:header
        :attributes="$attributes->prefixed('header:')->classes('gap-2')"
    >
        <tk:sidebar.toggle
            :attributes="$attributes->prefixed('sidebar-open:')->classes('lg:hidden')"
        />

        {{ $prepend ?? '' }}

        <tk:spacer :attributes="$attributes->prefixed('spacer:')" />

        {{ $append ?? '' }}
        {{ $search ?? '' }}
        {{ $notification ?? '' }}

        <tk:appearance.menu
            :attributes="$attributes->prefixed('appearance:')"
            :control="$appearance"
            :items="$userMenu"
        >
            {{ $avatarMenu ?? '' }}
        </tk:appearance.menu>
    </tk:header>

    <tk:main
        :attributes="$attributes->prefixed('main:')"
        container
    >
        {{ $slot }}
    </tk:main>

    @isset ($aside)
        <tk:aside
            :attributes="$attributes->prefixed('aside:')"
            sticky
        >
            {{ $aside }}
        </tk:aside>
    @endisset
</div>
