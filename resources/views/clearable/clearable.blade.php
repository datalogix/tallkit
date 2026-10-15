@props([
    ...TALLKit::elementProps(),
    'label' => null,
    'action' => null,
])
<tk:button
    wire:ignore.self
    x-cloak
    x-data="clearable({{ Js::from($action) }})"
    :$label
    :attributes="$attributes->withElementProps(get_defined_vars())
        ->merge(['variant' => 'none', 'icon' => 'close', 'tooltip' => $label || $slot->hasActualContent() ? null : 'Clear input'])
        ->classes(
            '
                min-w-6 min-h-6
                [[data-tallkit-field-control]:has([data-tallkit-input]:placeholder-shown)_&]:hidden
                [[data-tallkit-field-control]:has([data-tallkit-input]:disabled)_&]:hidden
                [[data-tallkit-field-control]:has([data-tallkit-input][readonly])_&]:hidden
            '
        )
    "
>
    {{ $slot }}
</tk:button>
