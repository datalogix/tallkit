@props([
    'mode' => null,
    'items' => null,
])
@if ($mode === 'toggle' || (($mode === null || $mode === true) && !($items || $slot->isNotEmpty())))
    <tk:appearance.toggle
        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'toggle:')"
    />
@endif

@if ($items || $slot->isNotEmpty())
    <tk:avatar.menu
        :attributes="$attributes->whereDoesntStartWith(['toggle:', 'menu-item:'])"
        :$items
    >
        {{ $slot ?? '' }}

        @if ($mode === 'selector' || $mode === null || $mode === true)
            <x-slot:prepend>
                <tk:appearance.menu-item
                    :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'menu-item:')"
                />

                <tk:menu.separator />
            </x-slot:prepend>
        @endif
    </tk:avatar.menu>
@endif
