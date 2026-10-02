@props([
    'control' => null,
    'items' => null,
])
@if ($control === 'toggle' || (($control === null || $control === true) && !($items || $slot->isNotEmpty())))
    <tk:appearance.toggle
        :attributes="$attributes->prefixed('toggle:')"
    />
@endif

@if ($items || $slot->isNotEmpty())
    <tk:avatar.menu
        :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), ['keepOpen' => null])->whereDoesntStartWith(['toggle:', 'menu-item:'])"
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
