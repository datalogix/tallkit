@aware(['list', 'size', 'variant'])
@props(['size', 'variant'])

@php
$square ??= $slot->isEmpty() && !$attributes->get('label');
@endphp

<tk:element
    :name="$baseComponentKey()"
    :icon:size="$adjustSize($size)"
    icon:class="me-1.5"
    icon-trailing:size="$adjustSize($size, move: -2)"
    icon-trailing:class="ms-1"
    badge:size="$adjustSize($size)"
    badge:class="ms-2"
    :content:class="$classes(
        'flex-1 leading-none whitespace-nowrap',
        match($size) {
            'xs' => 'text-[11px] font-normal',
            'sm' => 'text-xs font-normal',
            default => 'text-sm font-medium',
            'lg' => 'text-base font-medium',
            'xl' => 'text-lg font-semibold',
            '2xl' => 'text-xl font-semibold',
            '3xl' => 'text-xl font-bold',
        }
    )"
    :attributes="$attributes
        ->classes(
            '
            relative
            gap-none
            rounded-lg
            text-zinc-500 dark:text-white/80
            hover:text-zinc-800 dark:hover:text-white
            hover:bg-zinc-800/10 dark:hover:bg-white/10
            ',
            $square ? 'p-3' : 'p-2.5',
            $list ? 'w-full my-px' : 'data-current:after:absolute data-current:after:-bottom-3 data-current:after:inset-x-0 data-current:after:h-[2px]',
        )->classes(match ($variant) {
            'accent' => [
                'data-current:text-(--color-accent-content) hover:data-current:text-(--color-accent-content)',
                'hover:data-current:bg-[color-mix(in_oklab,_var(--color-accent-content),_transparent_90%)]',
                $list ? 'data-current:bg-zinc-100 dark:data-current:bg-white/10' : 'data-current:after:bg-(--color-accent-content)',
            ],
            default => [
                'data-current:text-zinc-800 dark:data-current:text-zinc-100',
                $list ? 'data-current:bg-zinc-100 dark:data-current:bg-white/10' : 'data-current:after:bg-zinc-800 dark:data-current:after:bg-white',
            ],
        })
    "
>
    {{ $slot }}
</tk:element>
