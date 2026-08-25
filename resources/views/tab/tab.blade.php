@aware(['size', 'variant'])
@props([
    'size' => null,
    'variant' => null,
    'name' => null,
    'selected' => null,
])
@php

$name ??= TALLKit::generateId('tab');

@endphp
<tk:button
    :attributes="$attributes
        ->classes(
            'shrink-0',
            TALLKit::paddingInline(size: $size),
            match ($variant) {
                'line' => '
                    -mb-px
                    border-b-2 border-transparent
                    [&[data-selected]]:border-zinc-800
                    dark:[&[data-selected]]:border-white
                ',
                'pills' => '
                    rounded-full
                    bg-zinc-800/10 dark:bg-white/5
                    [&[data-selected]]:bg-zinc-800 dark:[&[data-selected]]:bg-white
                    [&[data-selected]]:text-white dark:[&[data-selected]]:text-zinc-800
                ',
                'segmented' => '
                    rounded-md
                    [&[data-selected]]:bg-white dark:[&[data-selected]]:bg-white/20
                ',
                default => '
                    rounded-t-lg
                    border border-b-0
                    border-zinc-800/10 dark:border-white/20
                    [&[data-selected]]:bg-zinc-800 dark:[&[data-selected]]:bg-white
                    [&[data-selected]]:text-white dark:[&[data-selected]]:text-zinc-800
                '
            }
        )
        ->merge([
            'data-selected' => $selected ? '' : false,
            'wire:key' => $name,
            'data-name' => $name,
            'id' => TALLKit::generateId('tab', $name),
            'aria-controls' => TALLKit::generateId('tabpanel', $name),
            'role' => 'tab',
            ':tabindex' => 'isSelected(' . Js::from($name) . ') ? 0 : -1',
            ':aria-selected' => 'isSelected(' . Js::from($name) . ')',
            ':data-selected' => 'isSelected(' . Js::from($name) . ')',
            ':data-active' => 'isSelected(' . Js::from($name) . ')',
            'x-on:click' => 'select(' . Js::from($name) . ')',
        ])
    "
    :$size
    variant="none"
>
    {{ $slot }}
</tk:button>
