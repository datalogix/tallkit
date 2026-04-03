@props([
    'appearance' => null,
    'menu' => null,
    'user-menu' => null,
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

        @if (
            $appearance === 'toggle' ||
            (($appearance === null || $appearance === true) && !($userMenu || isset($avatarMenu)))
        )
            <tk:appearance.toggle
                :attributes="TALLKit::attributesAfter($attributes, 'appearance:')"
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
    </tk:header>

    <tk:main
        :attributes="TALLKit::attributesAfter($attributes, 'main:')"
        container
    >
        {{ $slot }}
    </tk:main>
</div>
