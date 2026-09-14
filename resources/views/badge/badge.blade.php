@props([
    'size' => null,
    'color' => null,
    'border' => null,
    'rounded' => null,
    'solid' => null,
    'close' => null,
    'label' => null,
])
<tk:element
    name="badge"
    x-data="badge"
    content:class="flex items-center"
    :$label
    :attributes="$attributes
        ->whereDoesntStartWith(['close:'])
        ->classes(
            '
                transition
                duration-300
                font-medium
                whitespace-nowrap
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
        )
    "
>
    {{ $slot }}

    @if ($close)
        <x-slot:append>
            <tk:badge.close
                :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'close:')"
                :$size
            />
        </x-slot:append>
    @endif
</tk:element>
