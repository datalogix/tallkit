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
        ->dataKey('badge-close')
        ->classes(
        '
            p-px -me-1
            text-current!
            opacity-60 hover:opacity-100
            shrink-0
        ',
        TALLKit::iconSize(size: $size),
    )"
>
    {{ $slot }}
</tk:button>
