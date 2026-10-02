@props([
    ...TALLKit::elementProps(),
    'size' => null,
    'scale' => null,
    'variant' => null,
    'color' => null,
    'label' => null,
    'level' => null,
])
<tk:element.wrapper
    kind="heading"
    :as="in_array((int) $level, [1, 2, 3, 4, 5, 6], true) ? 'h'.(int) $level : 'p'"
    :$label
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::elementProps())->classes(
        TALLKit::fontSize(size: $size, mode: $scale ?? 'largest', weight: true),
        '[:where(&)]:w-fit [&:has(+[data-tallkit-text])]:mb-2 [[data-tallkit-text]+&]:mt-2',
        TALLKit::text(color: $color) ?? match ($variant) {
            'none' => '',
            'accent' => 'text-[var(--color-accent-content)]',
            'strong' => TALLKit::textNeutral(variant: 'emphasis', prefix: '[:where(&)]:'),
            'subtle' => TALLKit::textNeutral(variant: 'subtle', prefix: '[:where(&)]:'),
            default => TALLKit::textNeutral(variant: 'strong', prefix: '[:where(&)]:'),
        },
    )"
>
    {{ $slot }}
</tk:element.wrapper>
