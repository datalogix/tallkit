<div {{ $attributes
    ->whereDoesntStartWith(['header:', 'area:', 'brand:', 'menu:', 'spacer:', 'appearance:', 'appearance-', 'user-menu:', 'sidebar:', 'sidebar-', 'main:'])
    ->classes('min-h-screen')
}}>
    <tk:header
        :attributes="$attributesAfter('header:')->classes(['flex-col items-start lg:-space-y-2' => isset($header)])"
    >
        <div {{ $attributesAfter('area:')->classes('flex-1 w-full flex items-center gap-2') }}>
            <tk:sidebar.toggle
                :attributes="$attributesAfter('sidebar-open:')->classes('lg:hidden')"
            />

            <tk:brand
                :attributes="$attributesAfter('brand:')->classes('max-lg:hidden me-4')"
            >
                {{ $brand ?? '' }}
            </tk:brand>

            {{ $prepend ?? '' }}

            @if ($align === 'center' || $align === 'right')
                <tk:spacer :attributes="$attributesAfter('spacer:')" />
            @endif

            <div class="hidden lg:block">
                @isset ($header)
                    {{ $header }}
                @else
                    <tk:nav
                        :attributes="$attributesAfter('menu:')"
                        :items="$menu"
                    >
                        {{ $nav ?? '' }}
                    </tk:nav>
                @endisset
            </div>

            @if ($align === 'center' || $align === 'left' || $align === null)
                <tk:spacer :attributes="$attributesAfter('spacer:')" />
            @endif

            {{ $append ?? '' }}
            {{ $search ?? '' }}

            @if ($appearance === 'toggle' || (($appearance === null || $appearance === true) && !($userMenu || isset($avatarMenu))))
                <tk:appearance.toggle
                    :attributes="$attributesAfter('appearance-toggle:')"
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
        </div>

        @isset ($header)
            <div class="hidden lg:block">
                <tk:nav
                    :attributes="$attributesAfter('menu:')"
                    :items="$menu"
                >
                    {{ $nav ?? '' }}
                </tk:nav>
            </div>
        @endisset
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

    <tk:main
        :attributes="$attributesAfter('main:')"
        container
    >
        {{ $slot }}
    </tk:main>
</div>
