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
            [[disabled]_&]:opacity-disabled [&[disabled]]:opacity-disabled

            [&[data-active]_[data-tallkit-icon]]:text-current
        ',
        TALLKit::textNeutral(variant: 'muted', prefix: '*:[data-tallkit-icon]:'),
        TALLKit::roundedSize(size: $size),
        TALLKit::paddingBlock(size: $size, mode: 'large'),
        TALLKit::paddingInline(size: $size, mode: 'largest'),
        match ($variant) {
            'danger' => TALLKit::classes(
                TALLKit::textNeutral(),
                TALLKit::text(color: 'red', prefix: 'data-active:'),
                'data-active:bg-red-50 dark:data-active:bg-red-400/20',
            ),
            default => TALLKit::classes(
                TALLKit::textNeutral(),
                TALLKit::textNeutral(variant: 'strong', prefix: 'data-active:'),
                'data-active:bg-zinc-100 dark:data-active:bg-white/10',
            ),
        },
    )->merge(['data-keep-open' => $keepOpen])"
>
    <x-slot:icon-empty>
        <div class="w-5 hidden [[data-tallkit-menu]:has(>[data-tallkit-menu-item-has-icon])_&]:block"></div>
    </x-slot:icon-empty>

    {{ $slot }}
</tk:element>
