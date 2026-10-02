@props([
    ...TALLKit::elementProps(),
    'size' => null,
    'color' => null,
    'border' => null,
    'rounded' => null,
    'solid' => null,
    'closable' => null,
    'label' => null,
])
@php

$closeIcon = is_string($closable) && $closable !== '' ? $closable : null;
$closable = $closable || Str::contains($slot, TALLKit::dataKey('badge-close'));

$linkAttributes = ['href', 'route', 'parameters', 'navigate', 'external', 'action', 'type', 'exact', 'match-query', 'current', 'target', 'rel', 'download', 'wire:navigate', 'wire:click'];
$split = $closable && $attributes->hasAny($linkAttributes);

$attributes = TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::elementProps());

$closeAttributes = $attributes->prefixed('close:')
    ->merge(is_string($label) && $label !== '' ? ['aria-label' => __('Remove').': '.__($label)] : []);

$linkOwn = fn ($key) => in_array($key, $linkAttributes, true)
    || in_array($key, array_map(Str::kebab(...), array_keys(TALLKit::elementProps())), true)
    || in_array($key, ['tooltip', 'aria-label', 'aria-current'], true)
    || Str::startsWith($key, ['tooltip:', 'icon:', 'icon-trailing:', 'icon-dot:', 'content:', 'prefix:', 'suffix:', 'kbd:', 'info:', 'badge:']);

$pill = ($split ? $attributes->filter(fn ($value, $key) => ! $linkOwn($key)) : $attributes)
    ->whereDoesntStartWith(['close:'])
    ->merge(['x-data' => $closable ? 'badge' : null])
    ->classes(
        '
            transition
            duration-300
            font-medium
            whitespace-nowrap
            max-w-full
            [print-color-adjust:exact]
        ',
        TALLKit::fontSize(size: $size),
        TALLKit::roundedSize(size: $rounded ? 'full' : $size),
        TALLKit::iconSize(size: $size),
        TALLKit::gap(size: $size),
        TALLKit::paddingInline(size: $size, mode: 'small'),
        TALLKit::paddingBlock(size: $size, mode: 'smallest'),
        TALLKit::borderStyle(style: $border),
    )
    ->when(
        $solid,
        fn ($c) => $c->classes(match ($color) {
            'accent' => 'text-[var(--color-accent-foreground)] bg-[var(--color-accent)] [&:is(button)]:hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_15%)]',
            'inverse' => 'text-white dark:text-zinc-700 bg-zinc-700 dark:bg-white [&:is(button)]:hover:bg-zinc-800 dark:[&:is(button)]:hover:bg-zinc-300',
            default => TALLKit::solidBackground(color: $color) ?? 'text-white bg-zinc-500 dark:bg-zinc-600 [&:is(button)]:hover:bg-zinc-600 dark:[&:is(button)]:hover:bg-zinc-500',
        }),
        fn ($c) => $c->classes(match ($color) {
            'accent' => '
                text-[var(--color-accent-foreground)]
                bg-[color-mix(in_oklab,_var(--color-accent),_transparent_20%)]
                dark:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_40%)]
                [&:is(button)]:hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_30%)]
                dark:[&:is(button)]:hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_50%)]
            ',
            'inverse' => 'text-white dark:text-zinc-700 bg-zinc-700/90 dark:bg-white/90 [&:is(button)]:hover:bg-zinc-500 dark:[&:is(button)]:hover:bg-zinc-300',
            default => (TALLKit::mutedText(color: $color) ?? TALLKit::mutedText(color: 'zinc'))
                .' '.(TALLKit::mutedBackground(color: $color, as: 'button') ?? TALLKit::mutedBackground(color: 'zinc', as: 'button')),
        })
    );

if ($split) {
    $pill = $pill->except('class')->merge(['class' => str_replace('[&:is(button)]:hover:', '[&:has(>[data-tallkit-badge-link]:hover)]:', (string) $pill->get('class'))], false);
}

@endphp
@if ($split)
    <tk:element
        kind="badge"
        as="span"
        :attributes="$pill"
    >
        <tk:element
            content:class="min-w-0 truncate [&:has(>*)]:flex [&:has(>*)]:items-center"
            :attributes="$attributes->filter(fn ($value, $key) => $linkOwn($key))
                ->dataKey('badge-link')
                ->classes('min-w-0 rounded-[inherit]')"
        >
            {{ $slot }}
        </tk:element>

        @if ($closable)
            <x-slot:append>
                <tk:badge.close :attributes="$closeAttributes" :$size :icon="$closeIcon" />
            </x-slot:append>
        @endif
    </tk:element>
@else
    <tk:element
        kind="badge"
        content:class="min-w-0 truncate [&:has(>*)]:flex [&:has(>*)]:items-center"
        :attributes="$pill"
    >
        {{ $slot }}

        @if ($closable)
            <x-slot:append>
                <tk:badge.close :attributes="$closeAttributes" :$size :icon="$closeIcon" />
            </x-slot:append>
        @endif
    </tk:element>
@endif
