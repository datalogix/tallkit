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

$closeIcon = string_value($closable);
$hasOwnClose = Str::contains($slot, TALLKit::dataKey('badge-close'));
$closable = $closable || $hasOwnClose;

$linkAttributes = ['href', 'route', 'parameters', 'navigate', 'external', 'action', 'type', 'exact', 'match-query', 'current', 'target', 'rel', 'download', 'wire:navigate', 'wire:click'];
$isClick = fn ($key) => Str::startsWith($key, ['wire:click', 'x-on:click', '@click']) && ! Str::contains($key, ['.outside', '.away']);
$hasClick = $attributes->filter(fn ($value, $key) => $isClick($key))->isNotEmpty();
$split = $closable && ($hasClick || $attributes->hasAny($linkAttributes) || in_array($attributes->get('as'), ['a', 'button'], true));

$attributes = $attributes->withElementProps(get_defined_vars());

$closeName = is_string($label) && $label !== '' ? __($label) : trim(strip_tags((string) $slot));
$closeAttributes = $attributes->prefixed('close:')
    ->merge($closeName !== '' ? ['aria-label' => __('Remove').': '.$closeName] : []);

$asButton = $hasClick && ! $attributes->hasAny(['href', 'route', 'as']);

$linkOwn = fn ($key) => in_array($key, $linkAttributes, true)
    || in_array($key, array_map(Str::kebab(...), array_keys(TALLKit::elementProps())), true)
    || in_array($key, ['as', 'disabled', 'tooltip', 'aria-label', 'aria-current'], true)
    || $isClick($key)
    || Str::startsWith($key, ['tooltip:', 'icon:', 'icon-trailing:', 'icon-dot:', 'content:', 'prefix:', 'suffix:', 'kbd:', 'info:', 'badge:']);

$pill = ($split ? $attributes->filter(fn ($value, $key) => ! $linkOwn($key)) : $attributes)
    ->whereDoesntStartWith(['close:'])
    ->merge(['x-data' => $closable ? 'badge' : null])
    ->when(! $split && $asButton, fn ($attrs) => $attrs->merge(['as' => 'button']))
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
    ->when($split, fn ($attrs) => $attrs->classes('relative'))
    ->when($border, fn ($c) => $c->classes(match (true) {
        (bool) $solid => 'border-black/10 dark:border-white/10',
        $color === 'accent' => 'border-[var(--color-accent)]',
        $color === 'inverse' => 'border-zinc-700 dark:border-white',
        default => 'border-(--tk-border)',
    }))
    ->when(
        $solid,
        fn ($c) => $c->classes(match ($color) {
            'accent' => 'text-[var(--color-accent-foreground)] bg-[var(--color-accent)] [&:is(a,button,:has(>[data-tallkit-badge-link]:hover))]:hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_15%)]',
            'inverse' => 'text-white dark:text-zinc-700 bg-zinc-700 dark:bg-white [&:is(a,button,:has(>[data-tallkit-badge-link]:hover))]:hover:bg-zinc-800 dark:[&:is(a,button,:has(>[data-tallkit-badge-link]:hover))]:hover:bg-zinc-300',
            default => TALLKit::solidBackground(color: $color, as: 'a,button,:has(>[data-tallkit-badge-link]:hover)') ?? 'text-white bg-zinc-500 dark:bg-zinc-600 [&:is(a,button,:has(>[data-tallkit-badge-link]:hover))]:hover:bg-zinc-600 dark:[&:is(a,button,:has(>[data-tallkit-badge-link]:hover))]:hover:bg-zinc-500',
        }),
        fn ($c) => $c->classes(match ($color) {
            'accent' => '
                text-[var(--color-accent-foreground)]
                bg-[color-mix(in_oklab,_var(--color-accent),_transparent_20%)]
                dark:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_40%)]
                [&:is(a,button,:has(>[data-tallkit-badge-link]:hover))]:hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_30%)]
                dark:[&:is(a,button,:has(>[data-tallkit-badge-link]:hover))]:hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_50%)]
            ',
            'inverse' => 'text-white dark:text-zinc-700 bg-zinc-700/90 dark:bg-white/90 [&:is(a,button,:has(>[data-tallkit-badge-link]:hover))]:hover:bg-zinc-500 dark:[&:is(a,button,:has(>[data-tallkit-badge-link]:hover))]:hover:bg-zinc-300',
            default => (TALLKit::mutedText(color: $color) ?? TALLKit::mutedText(color: 'zinc'))
                .' '.(TALLKit::mutedBackground(color: $color, as: 'a,button,:has(>[data-tallkit-badge-link]:hover)') ?? TALLKit::mutedBackground(color: 'zinc', as: 'a,button,:has(>[data-tallkit-badge-link]:hover)')),
        })
    );

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
                ->when($asButton, fn ($attrs) => $attrs->merge(['as' => 'button']))
                ->dataKey('badge-link')
                ->classes('min-w-0 rounded-[inherit] after:absolute after:inset-0 after:rounded-[inherit]')"
        >
            {{ $slot }}
        </tk:element>

        @if ($closable && ! $hasOwnClose)
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

        @if ($closable && ! $hasOwnClose)
            <x-slot:append>
                <tk:badge.close :attributes="$closeAttributes" :$size :icon="$closeIcon" />
            </x-slot:append>
        @endif
    </tk:element>
@endif
