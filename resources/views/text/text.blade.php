@props([
    ...TALLKit::elementProps(),
    'size' => null,
    'scale' => null,
    'weight' => null,
    'variant' => null,
    'color' => null,
])
<tk:element.wrapper
    kind="text"
    as="p"
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::elementProps())->classes(
        TALLKit::fontSize(size: $size, mode: $scale, weight: $weight),
        TALLKit::iconSize(size: $size, mode: $scale),
        TALLKit::text(color: $color) ?? match ($variant) {
            'accent' => 'text-[var(--color-accent-content)]',
            'strong' => TALLKit::textNeutral(variant: 'strong', prefix: '[:where(&)]:'),
            'subtle' => TALLKit::textNeutral(variant: 'subtle', prefix: '[:where(&)]:'),
            'none' => '',
            default => TALLKit::textNeutral(prefix: '[:where(&)]:'),
        }
    )"
>
    {{ $slot }}
</tk:element.wrapper>
