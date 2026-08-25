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
        :attributes="TALLKit::attributesAfter($attributes, 'sidebar:')"
        sticky
        stashable
    >
        <tk:sidebar.toggle
            :attributes="TALLKit::attributesAfter($attributes, 'sidebar-close:')->classes('lg:hidden')"
            icon="close"
        />

        <tk:brand
            :attributes="TALLKit::attributesAfter($attributes, 'sidebar-brand:')"
            size="lg"
        >
            {{ $brand ?? '' }}
        </tk:brand>

        {{ $prepend ?? '' }}
        {{ $header ?? '' }}

        <tk:nav
            :attributes="TALLKit::attributesAfter($attributes, 'sidebar-menu:')"
            :items="$menu"
            list
        >
            {{ $nav ?? '' }}
        </tk:nav>

        {{ $sidebar ?? '' }}
        {{ $append ?? '' }}
    </tk:sidebar>

    <tk:header
        :attributes="TALLKit::attributesAfter($attributes, 'header:')->classes('gap-2')"
    >
        <tk:sidebar.toggle
            :attributes="TALLKit::attributesAfter($attributes, 'sidebar-open:')->classes('lg:hidden')"
        />

        {{ $prepend ?? '' }}

        <tk:spacer :attributes="TALLKit::attributesAfter($attributes, 'spacer:')" />

        {{ $append ?? '' }}
        {{ $search ?? '' }}
        {{ $notification ?? '' }}

        <tk:appearance.menu
            :attributes="TALLKit::attributesAfter($attributes, 'appearance:')"
            :mode="$appearance"
            :items="$userMenu"
        >
            {{ $avatarMenu ?? '' }}
        </tk:appearance.menu>
    </tk:header>

    <tk:main
        :attributes="TALLKit::attributesAfter($attributes, 'main:')"
        container
    >
        {{ $slot }}
    </tk:main>

    @isset ($aside)
        <tk:aside
            :attributes="TALLKit::attributesAfter($attributes, 'aside:')"
            sticky
        >
            {{ $aside }}
        </tk:aside>
    @endisset
</div>
