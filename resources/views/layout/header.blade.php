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
        :attributes="TALLKit::attributesAfter($attributes, 'header:')->classes(['flex-col items-start' => isset($header)])"
    >
        <div {{ TALLKit::attributesAfter($attributes, 'area:')->classes('flex-1 w-full flex items-center gap-2') }}>
            <tk:sidebar.toggle
                :attributes="TALLKit::attributesAfter($attributes, 'sidebar-open:')->classes('lg:hidden')"
            />

            <tk:brand
                :attributes="TALLKit::attributesAfter($attributes, 'brand:')->classes('max-lg:hidden me-4')"
            >
                {{ $brand ?? '' }}
            </tk:brand>

            {{ $prepend ?? '' }}

            @if ($align === 'center' || $align === 'right')
                <tk:spacer :attributes="TALLKit::attributesAfter($attributes, 'spacer:')" />
            @endif

            <div class="hidden lg:block">
                @isset ($header)
                    {{ $header }}
                @else
                    <tk:nav
                        :attributes="TALLKit::attributesAfter($attributes, 'menu:')"
                        :items="$menu"
                    >
                        {{ $nav ?? '' }}
                    </tk:nav>
                @endisset
            </div>

            @if ($align === 'center' || $align === 'left' || $align === null)
                <tk:spacer :attributes="TALLKit::attributesAfter($attributes, 'spacer:')" />
            @endif

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
        </div>

        @if (isset($header) && ((isset($nav) && filled($nav)) || ($menu && filled($menu))))
            <div class="hidden lg:block">
                <tk:nav
                    :attributes="TALLKit::attributesAfter($attributes, 'menu:')"
                    :items="$menu"
                    indicator="line-bottom"
                >
                    {{ $nav ?? '' }}
                </tk:nav>
            </div>
        @endif
    </tk:header>

    <tk:sidebar
        :attributes="TALLKit::attributesAfter($attributes, 'sidebar:')->classes('lg:hidden')"
        sticky
        stashable
    >
        <tk:sidebar.toggle
            :attributes="TALLKit::attributesAfter($attributes, 'sidebar-close:')->classes('lg:hidden')"
            icon="close"
        />

        <tk:brand
            :attributes="TALLKit::attributesAfter($attributes, 'sidebar-brand:')"
        >
            {{ $brand ?? '' }}
        </tk:brand>

        {{ $prepend ?? '' }}
        {{ $header ?? '' }}

        <tk:nav
            :attributes="TALLKit::attributesAfter($attributes, 'sidebar-menu:')"
            :items="$menu"
            :indicator="false"
            list
        >
            {{ $nav ?? '' }}
        </tk:nav>

        {{ $sidebar ?? '' }}
        {{ $append ?? '' }}
    </tk:sidebar>

    <tk:main
        :attributes="TALLKit::attributesAfter($attributes, 'main:')"
        container
    >
        {{ $slot }}
    </tk:main>
</div>
