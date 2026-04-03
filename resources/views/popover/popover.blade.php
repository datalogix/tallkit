@props([
    'size' => null,
    'keep-open' => null,
])
@php

$keepOpen = ${'keep-open'} ?? $attributes->pluck('keepOpen');

@endphp
<div
    popover="manual"
    {{
        $attributes->classes(
            '
                [:where(&)]:min-w-48 shadow-xs
                border border-zinc-200 dark:border-white/10
                bg-white dark:bg-zinc-700
                text-zinc-800 dark:text-white
            ',
            TALLKit::padding(size: $size, mode: 'small'),
            TALLKit::roundedSize(size: $size, mode: 'large'),
        )->merge(['data-keep-open' => $keepOpen])
    }}
>
    {{ $slot }}
</div>
