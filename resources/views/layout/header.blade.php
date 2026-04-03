@props([
    'appearance' => null,
    'menu' => null,
    'user-menu' => null,
    'align' => null,
])
@php

$userMenu = ${'user-menu'} ?? $attributes->pluck('userMenu');

@endphp
<div
    {{
        $attributes
            ->whereDoesntStartWith([
                'header:', 'area:', 'brand:', 'menu:', 'spacer:',
                'appearance', 'user-menu:', 'sidebar', 'main:',
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

            @if ($appearance === 'toggle' || (($appearance === null || $appearance === true) && !($userMenu || isset($avatarMenu))))
                <tk:appearance.toggle
                    :attributes="TALLKit::attributesAfter($attributes, 'appearance-toggle:')"
                />
            @endif

            {{ $notification ?? '' }}

            @if ($userMenu || isset($avatarMenu))
                <tk:avatar.menu
                    :attributes="TALLKit::attributesAfter($attributes, 'user-menu:')"
                    :items="$userMenu"
                >
                    {{ $avatarMenu ?? '' }}

                    @if ($appearance === 'selector' || $appearance === null || $appearance === true)
                        <x-slot:prepend>
                            <tk:appearance.menu-item
                                :attributes="TALLKit::attributesAfter($attributes, 'appearance-menu-item:')"
                            />

                            <tk:menu.separator />
                        </x-slot:prepend>
                    @endif
                </tk:avatar.menu>
            @endif
        </div>

        @if (isset($header) && ((isset($nav) && filled($nav)) || ($menu && filled($menu))))
            <div class="hidden lg:block -mt-2">
                <tk:nav
                    :attributes="TALLKit::attributesAfter($attributes, 'menu:')"
                    :items="$menu"
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
