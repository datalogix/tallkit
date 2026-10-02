@props([
    ...TALLKit::elementProps(),
    'icon' => null,
    'size' => null,
])
<tk:button
    :icon="$slot->isEmpty() ? $icon ?? 'close' : null"
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::elementProps())
        ->merge(['variant' => 'none', 'tooltip' => 'Close'])
        ->dataKey('dismissible')
        ->dataKey('alert-close')
        ->classes(
            TALLKit::iconSize(size: $size),
            'p-1 focus-visible:ring-2 rounded',
            'text-(--tk-on-soft) hover:bg-(--tk-soft-hover) focus-visible:ring-(--tk-ring)',
        )
    "
>
    {{ $slot }}
</tk:button>
