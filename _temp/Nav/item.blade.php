@aware(['list', 'size', 'variant'])
@props(['size', 'variant'])

@php
$square ??= $slot->isEmpty() && !$attributes->get('label');
@endphp

<tk:element
    :name="$baseComponentKey()"
    :icon:size="$adjustSize(size: $size)"
    :icon:class="$square ? '' : 'me-1.5'"
    :icon-dot:class="'me-1'"
    :icon-trailing:size="$adjustSize(size: $size)"
    :icon-trailing:class="$square ? '' : 'ms-1.5'"
    :badge:size="$adjustSize(size: $size)"
    :badge:class="$square ? '' : 'ms-2'"
    :content:class="$classes(
        'flex-1 leading-none whitespace-nowrap',
        $fontSize(size: $size, weight: true)
    )"
    :attributes="$attributes
        ->classes(
            $roundedSize(size: $size),
            '
                relative
                [&:is(a,button)]:hover:text-zinc-800 dark:[&:is(a,button)]:hover:text-white
                [&:is(a,button)]:hover:bg-zinc-800/10 dark:[&:is(a,button)]:hover:bg-white/10
                [&[disabled]]:opacity-75 dark:[&[disabled]]:opacity-50
                [&[disabled]]:cursor-default [&[disabled]]:pointer-events-none
            ',
            match ($square) {
                true => 'p-3',
                default => 'p-2.5',
            },
            match ($list) {
                true => 'w-full my-px',
                default => '
                    data-current:after:absolute
                    data-current:after:-bottom-3
                    data-current:after:inset-x-0
                    data-current:after:h-[2px]
                ',
            }
        )
        ->classes(match ($variant) {
            'accent' => [
                'data-current:text-(--color-accent-content) hover:data-current:text-(--color-accent-content)',
                'hover:data-current:bg-[color-mix(in_oklab,_var(--color-accent-content),_transparent_90%)]',
                $list
                    ? 'data-current:bg-[color-mix(in_oklab,_var(--color-accent-content),_transparent_90%)]'
                    : 'data-current:after:bg-(--color-accent-content)',
            ],
            default => [
                'data-current:text-zinc-800 dark:data-current:text-white',
                $list
                    ? 'data-current:bg-zinc-800/10 dark:data-current:bg-white/10'
                    : 'data-current:after:bg-zinc-800 dark:data-current:after:bg-white',
            ],
        })
    "
>
    {{ $slot }}
</tk:element>
