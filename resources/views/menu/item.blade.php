@aware(['size'])
@props([
    ...TALLKit::elementProps(),
    'variant' => null,
    'size' => null,
    'keepOpen' => null,
])
<tk:element
    kind="menu-item"
    role="menuitem"
    :icon:size="TALLKit::adjustSize(size: $size)"
    :icon-trailing:size="TALLKit::adjustSize(size: $size)"
    :icon-trailing:class="'text-zinc-400 [[data-tallkit-icon]:hover_&]:text-current'"
    :badge:size="TALLKit::adjustSize(size: $size)"
    :content:class="TALLKit::classes(
        'block flex-1 min-w-0 truncate leading-none text-start',
        TALLKit::fontSize(size: $size, weight: true),
    )"
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::elementProps())->classes(
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
                'tk-color-red data-active:text-(--tk-on-soft) data-active:bg-(--tk-soft)',
            ),
            default => TALLKit::classes(
                TALLKit::textNeutral(),
                TALLKit::textNeutral(variant: 'strong', prefix: 'data-active:'),
                'data-active:bg-zinc-100 dark:data-active:bg-white/10',
            ),
        },
    )->merge(['as' => 'button', 'data-keep-open' => $keepOpen])"
>
    <x-slot:icon-empty>
        <span class="w-5 hidden [[data-tallkit-menu]:has(>[data-tallkit-menu-item-has-icon])_&]:block"></span>
    </x-slot:icon-empty>

    {{ $slot }}
</tk:element>
