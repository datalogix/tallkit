@props([
    'label' => null,
    'value' => null,
    'size' => null,
])
<tk:text
    :attributes="$attributes
        ->dataKey('slider-tick')
        ->classes(
            'flex items-center justify-center pointer-events-none',
            TALLKit::generateClassBySize(size: $size, name: 'min-w', values: ['4', '4.5', '5', '5.5', '6', '7', '8']),
        )
        ->merge(['data-value' => $value])
    "
    :$label
    :$size
>
    @if ($slot->hasActualContent() || $label)
        {{ $slot }}
    @else
        <span class="h-1.5 w-px bg-black/25 dark:bg-white/25"></span>
    @endif
</tk:text>
