@aware(['size', 'variant', 'orientation'])
@props([
    'size' => null,
    'variant' => null,
    'name' => null,
    'selected' => null,
])
@php

$name ??= TALLKit::generateId(prefix: 'tab');

@endphp
<tk:button
    :attributes="$attributes
        ->classes(
            'shrink-0',
            TALLKit::padding(size: $size),
            match ($variant) {
                'line' => '
                    border-transparent
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
                    border-zinc-800/10 dark:border-white/20
                    [&[data-selected]]:bg-zinc-800 dark:[&[data-selected]]:bg-white
                    [&[data-selected]]:text-white dark:[&[data-selected]]:text-zinc-800
                ',
            },
        )
        ->when(
            $orientation === 'vertical',
            fn ($attrs) => $attrs->classes(
                match ($variant) {
                    'line' => '-mr-px border-r-2',
                    'pills' => '',
                    'segmented' => '',
                    default => 'rounded-l-lg border border-r-0',
                },
            ),
            fn ($attrs) => $attrs->classes(
                match ($variant) {
                    'line' => '-mb-px border-b-2',
                    'pills' => '',
                    'segmented' => '',
                    default => 'rounded-t-lg border border-b-0',
                },
            ),
        )
        ->merge([
            'data-selected' => $selected ? '' : false,
            'wire:key' => $name,
            'data-name' => $name,
            'id' => TALLKit::generateId(prefix: 'tab', name: $name),
            'aria-controls' => TALLKit::generateId(prefix: 'tabpanel', name: $name),
            'role' => 'tab',
            ':tabindex' => 'isSelected(' . Js::from($name) . ') ? 0 : -1',
            ':aria-selected' => 'isSelected(' . Js::from($name) . ')',
            ':data-selected' => 'isSelected(' . Js::from($name) . ')',
            'x-on:click' => 'select(' . Js::from($name) . ')',
        ])
    "
    :$size
    variant="none"
>
    {{ $slot }}
</tk:button>
