@aware(['list', 'size', 'variant', 'indicator'])
@props([
    'size' => null,
    'variant' => null,
    'square' => null,
])
@php

$list = (bool) $list;
$square ??= $slot->isEmpty() && !$attributes->get('label');

@endphp
<tk:element
    name="nav-item"
    :icon:size="TALLKit::adjustSize(size: $size)"
    :icon:class="$square ? '' : 'me-1.5'"
    :icon-dot:class="'me-1'"
    :icon-trailing:size="TALLKit::adjustSize(size: $size)"
    :icon-trailing:class="$square ? '' : 'ms-1.5'"
    :badge:size="TALLKit::adjustSize(size: $size)"
    :badge:class="$square ? '' : 'ms-2'"
    :content:class="TALLKit::classes(
        'flex-1 leading-none whitespace-nowrap justify-start text-start',
        TALLKit::fontSize(size: $size, weight: true)
    )"
    :attributes="$attributes
        ->classes([
            TALLKit::padding(size: $size),
            TALLKit::roundedSize(size: $size),
            '
                relative
                [&:is(a,button)]:hover:text-zinc-800 dark:[&:is(a,button)]:hover:text-white
                [&[disabled]]:opacity-50 dark:[&[disabled]]:opacity-40
                [&[disabled]]:cursor-default [&[disabled]]:pointer-events-none
            ',
            '[&:is(a,button)]:hover:bg-zinc-800/10 dark:[&:is(a,button)]:hover:bg-white/10' => $indicator && $indicator !== 'bg',
            match ($list) {
                true => 'w-full justify-start',
                default => '',
            },
            match ($variant) {
                'accent' => '
                    data-current:text-(--color-accent-content)
                    hover:data-current:text-(--color-accent-content)
                    hover:data-current:bg-[color-mix(in_oklab,_var(--color-accent-content),_transparent_90%)]
                ',
                default => 'data-current:text-zinc-800 dark:data-current:text-white',
            },
        ])
        ->when(
            $indicator === false,
            fn ($c) => $c->classes(
                match ($variant) {
                    'accent' => 'data-current:bg-[color-mix(in_oklab,_var(--color-accent-content),_transparent_90%)]',
                    default => 'data-current:bg-zinc-800/10 dark:data-current:bg-white/10',
                }
            )
        )
    "
>
    {{ $slot }}
</tk:element>
