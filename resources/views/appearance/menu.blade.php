@props([
    'control' => null,
    'items' => null,
])
@if ($control === 'toggle' || (($control === null || $control === true) && !($items || has_content($slot))))
    <tk:appearance.toggle
        :attributes="$attributes->prefixed('toggle:')"
    />
@endif

@if ($items || has_content($slot))
    <tk:avatar.menu
        :attributes="$attributes->withProps(get_defined_vars(), ['keepOpen' => null])->whereDoesntStartWith(['toggle:', 'menu-item:'])"
        :$items
    >
        {{ $slot ?? '' }}

        @if ($control === 'selector' || $control === null || $control === true)
            <x-slot:prepend>
                <tk:appearance.menu-item
                    :attributes="$attributes->prefixed('menu-item:')"
                />

                <tk:menu.separator />
            </x-slot:prepend>
        @endif
    </tk:avatar.menu>
@endif
