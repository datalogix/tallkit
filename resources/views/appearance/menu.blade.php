@props([
    'mode' => null,
    'items' => null,
])
@if ($mode === 'toggle' || (($mode === null || $mode === true) && !($items || isset($menu))))
    <tk:appearance.toggle
        :attributes="TALLKit::attributesAfter($attributes, 'toggle:')"
    />
@endif

{{ $slot }}

@if ($items || isset($menu))
    <tk:avatar.menu
        :attributes="$attributes->whereDoesntStartWith(['toggle:', 'menu-item:'])"
        :$items
    >
        {{ $menu ?? '' }}

        @if ($mode === 'selector' || $mode === null || $mode === true)
            <x-slot:prepend>
                <tk:appearance.menu-item
                    :attributes="TALLKit::attributesAfter($attributes, 'menu-item:')"
                />

                <tk:menu.separator />
            </x-slot:prepend>
        @endif
    </tk:avatar.menu>
@endif
