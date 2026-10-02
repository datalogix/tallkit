@props([
    'appearance' => null,
    'menu' => null,
    'userMenu' => null,
    'align' => null,
])
<div
    {{
        $attributes
            ->whereDoesntStartWith([
                'header:', 'area:', 'brand:', 'menu:', 'spacer:',
                'appearance:', 'sidebar', 'main:',
            ])
            ->classes('min-h-screen')
    }}
>
    <tk:header
        :attributes="$attributes->prefixed('header:')->classes(['flex-col items-start' => isset($header)])"
    >
        <div {{ $attributes->prefixed('area:')->classes('flex-1 w-full flex items-center gap-2') }}>
            <tk:sidebar.toggle
                :attributes="$attributes->prefixed('sidebar-open:')->classes('lg:hidden')"
            />

            <tk:brand
                :attributes="$attributes->prefixed('brand:')->classes('max-lg:hidden me-4')"
            >
                {{ $brand ?? '' }}
            </tk:brand>

            {{ $prepend ?? '' }}

            @if ($align === 'center' || $align === 'right')
                <tk:spacer :attributes="$attributes->prefixed('spacer:')" />
            @endif

            <div class="hidden lg:block">
                @isset ($header)
                    {{ $header }}
                @else
                    <tk:nav
                        :attributes="$attributes->prefixed('menu:')->merge(['label' => 'Main menu'])"
                        :items="$menu"
                    >
                        {{ $nav ?? '' }}
                    </tk:nav>
                @endisset
            </div>

            @if ($align === 'center' || $align === 'left' || $align === null)
                <tk:spacer :attributes="$attributes->prefixed('spacer:')" />
            @endif

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
        </div>

        @if (isset($header) && ((isset($nav) && filled($nav)) || ($menu && filled($menu))))
            <div class="hidden lg:block">
                <tk:nav
                    :attributes="$attributes->prefixed('menu:')->merge(['label' => 'Main menu'])"
                    :items="$menu"
                    indicator="line-bottom"
                >
                    {{ $nav ?? '' }}
                </tk:nav>
            </div>
        @endif
    </tk:header>

    <tk:sidebar
        :attributes="$attributes->prefixed('sidebar:')->classes('lg:hidden')"
        sticky
        stashable
    >
        <tk:sidebar.toggle
            :attributes="$attributes->prefixed('sidebar-close:')->classes('lg:hidden')"
            icon="close"
        />

        <tk:brand
            :attributes="$attributes->prefixed('sidebar-brand:')"
        >
            {{ $brand ?? '' }}
        </tk:brand>

        {{ $header ?? '' }}

        <tk:nav
            :attributes="$attributes->prefixed('sidebar-menu:')->merge(['label' => 'Main menu'])"
            :items="$menu"
            :indicator="false"
            list
        >
            {{ $nav ?? '' }}
        </tk:nav>

        {{ $sidebar ?? '' }}
    </tk:sidebar>

    <tk:main
        :attributes="$attributes->prefixed('main:')"
        container
    >
        {{ $slot }}
    </tk:main>
</div>
