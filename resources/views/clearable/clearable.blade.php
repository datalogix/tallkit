@props([
    ...TALLKit::elementProps(),
    'label' => null,
])
<tk:button
    wire:ignore.self
    x-cloak
    x-data="clearable()"
    :$label
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::elementProps())
        // Merged, not pluck(): a bound attribute runs more than once.
        ->merge(['variant' => 'none', 'tooltip' => 'Clear input'])
        ->classes('
        min-w-6 min-h-6
        [[data-tallkit-control]:has(:placeholder-shown)_&]:hidden
        [[data-tallkit-control]:has(:disabled)_&]:hidden
        [[data-tallkit-control]:has([readonly])_&]:hidden
    ')"
    icon="close"
>
    {{ $slot }}
</tk:button>
