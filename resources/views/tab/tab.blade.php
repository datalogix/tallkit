@aware(['size', 'variant', 'orientation'])
@props([
    ...TALLKit::elementProps(),
    'size' => null,
    'variant' => null,
    'name' => null,
    'selected' => null,
    'label' => null,
    'badge' => null,
])
@php

$name ??= TALLKit::stableId('tab');

$tabId = TALLKit::stableId('tab', $name);
$panelId = $tabId.'-panel';

@endphp
<tk:button
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::elementProps())
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
            'wire:key' => $tabId,
            'data-name' => $name,
            'id' => $tabId,
            'aria-controls' => $panelId,
            'role' => 'tab',
            ':tabindex' => 'isSelected(' . Js::from($name) . ') ? 0 : -1',
            ':aria-selected' => 'isSelected(' . Js::from($name) . ')',
            ':data-selected' => 'isSelected(' . Js::from($name) . ')',
            'x-on:click' => 'select(' . Js::from($name) . ')',
        ])
    "
    :$size
    :$label
    :$badge
    variant="none"
>
    {{ $slot }}
</tk:button>
