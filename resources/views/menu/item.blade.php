@aware(['size'])
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
    role="menuitem"
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
    :icon-trailing:size="TALLKit::adjustSize(size: $size)"
    :icon-trailing:class="'text-zinc-400 [[data-tallkit-icon]:hover_&]:text-current'"
    :badge:size="TALLKit::adjustSize(size: $size)"
    :content:class="TALLKit::classes(
        'flex-1 leading-none whitespace-nowrap justify-start text-start',
        TALLKit::fontSize(size: $size, weight: true),
    )"
    :attributes="$attributes->classes(
        '
            w-full focus:outline-hidden
            [[disabled]_&]:opacity-50 [&[disabled]]:opacity-50

            *:[data-tallkit-icon]:text-zinc-400
            dark:*:[data-tallkit-icon]:text-white/60
            [&[data-active]_[data-tallkit-icon]]:text-current
        ',
        TALLKit::roundedSize(size: $size),
        TALLKit::paddingBlock(size: $size, mode: 'large'),
        TALLKit::paddingInline(size: $size, mode: 'largest'),
        match ($variant) {
            'danger' => 'text-zinc-700 data-active:text-red-500 data-active:bg-red-50 dark:text-white/80 dark:data-active:bg-red-400/20 dark:data-active:text-red-200',
            default => 'text-zinc-700 dark:text-white/80 data-active:text-zinc-800 dark:data-active:text-white data-active:bg-zinc-100 dark:data-active:bg-white/10',
        },
    )->merge(['data-keep-open' => $keepOpen])"
>
    <x-slot:icon-empty>
        <div class="w-5 hidden [[data-tallkit-menu]:has(>[data-tallkit-menu-item-has-icon])_&]:block"></div>
    </x-slot:icon-empty>

    {{ $slot }}
</tk:element>
