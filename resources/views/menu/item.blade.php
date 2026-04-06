@props([
    'as' => null,
    'variant' => null,
    'size' => null,
    'keepOpen' => null,

    // element
    'label' => null,
    'icon' => null,
    'prefix' => null,
    'suffix' => null,
    'iconTrailing' => null,
    'info' => null,
    'badge' => null,
    'prepend' => null,
    'append' => null,
    'kbd' => null,
])
<tk:element
    name="menu-item"
    :$label
    :$icon
    :$prefix
    :$suffix
    :$iconTrailing
    :$info
    :$badge
    :$prepend
    :$append
    :$kbd
    :as="$as ?? 'button'"
    :icon:size="TALLKit::adjustSize(size: $size)"
    :icon:class="'me-1.5'"
    :icon-dot:class="'me-1'"
    :icon-trailing:size="TALLKit::adjustSize(size: $size)"
    :icon-trailing:class="'ms-1.5 text-zinc-400 [[data-tallkit-menu-item-icon-trailing]:hover_&]:text-current'"
    :badge:size="TALLKit::adjustSize(size: $size)"
    :badge:class="'ms-2'"
    :content:class="TALLKit::classes(
        'flex-1 leading-none whitespace-nowrap',
        TALLKit::fontSize(size: $size, weight: true),
    )"
    :attributes="$attributes->classes(
        '
            w-full focus:outline-hidden
            text-start
            [[disabled]_&]:opacity-50 [&[disabled]]:opacity-50

            *:[data-tallkit-menu-item-icon]:text-zinc-400
            dark:*:[data-tallkit-menu-item-icon]:text-white/60
            [&[data-active]_[data-tallkit-menu-item-icon]]:text-current

            *:[data-tallkit-menu-item-icon-trailing]:text-zinc-400
            dark:*:[data-tallkit-menu-item-icon-trailing]:text-white/60
            [&[data-active]_[data-tallkit-menu-item-icon-trailing]]:text-current
        ',
        TALLKit::roundedSize(size: $size),
        match ($size) {
            'xs' => 'px-3 py-2',
            'sm' => 'px-3.5 py-2.5',
            default => 'px-4 py-3',
            'lg' => 'px-4.5 py-3.5',
            'xl' => 'px-5 py-4',
            '2xl' => 'px-5.5 py-4.5',
            '3xl' => 'px-6 py-5',
        },
        match ($variant) {
            'danger' => 'text-zinc-500 data-active:text-red-500 data-active:bg-red-50 dark:text-white/80 dark:data-active:bg-red-400/20 dark:data-active:text-red-200',
            default => 'text-zinc-500 dark:text-white/80 data-active:text-zinc-800 dark:data-active:text-white data-active:bg-zinc-100 dark:data-active:bg-white/10',
        },
    )->merge(['data-keep-open' => $keepOpen, 'role' => 'menuitem'])"
>
    <x-slot:icon-empty>
        <div class="w-7 hidden [[data-tallkit-menu]:has(>[data-tallkit-menu-item-has-icon])_&]:block"></div>
    </x-slot:icon-empty>

    {{ $slot }}
</tk:element>
