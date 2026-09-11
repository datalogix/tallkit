@props([
    'size' => null,
    'mode' => null,
    'variant' => null,
])
<tk:element.wrapper
    name="heading"
    as="p"
    :attributes="$attributes->classes(
        TALLKit::fontSize(size: $size, mode: $mode ?? 'largest', weight: true),
        '[:where(&)]:w-fit [&:has(+[data-tallkit-text])]:mb-2 [[data-tallkit-text]+&]:mt-2',
        match ($variant) {
            'none' => '',
            'accent' => 'text-[var(--color-accent-content)]',
            'strong' => TALLKit::textNeutral(variant: 'emphasis', prefix: '[:where(&)]:'),
            'subtle' => TALLKit::textNeutral(variant: 'subtle', prefix: '[:where(&)]:'),
            default => TALLKit::text(color: $variant) ?? TALLKit::textNeutral(variant: 'strong', prefix: '[:where(&)]:'),
        },
    )"
>
    {{ $slot }}
</tk:element.wrapper>
