@props([
    ...TALLKit::elementProps(),
    'icon' => null,
    'size' => null,
])
<tk:button
    :icon="$slot->isEmpty() ? $icon ?? 'close' : null"
    :attributes="$attributes->withElementProps(get_defined_vars())->except('icon')
        ->dataKey('dismissible')
        ->dataKey('badge-close')
        ->classes(
        '
            z-1 p-px -me-1
            text-current!
            opacity-60 hover:opacity-100
            shrink-0
        ',
        TALLKit::iconSize(size: $size),
    )"
    variant="none"
    tooltip="Close"
>
    {{ $slot }}
</tk:button>
