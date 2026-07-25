@props([
    'icon' => null,
    'size' => null,
])
<tk:button
    variant="none"
    tooltip="Close"
    :icon="$slot->isEmpty() ? $icon ?? 'close' : null"
    :attributes="$attributes
        ->dataKey('dismissible')
        ->classes(
        '
            p-px -me-1
            text-current!
            opacity-50 hover:opacity-100
            shrink-0
        ',
        TALLKit::iconSize(size: $size),
    )"
>
    {{ $slot }}
</tk:button>
