<tk:element.wrapper
    :name="$baseComponentKey()"
    :attributes="$attributes->classes(
        $fontSize(size: $size, mode: $mode ?? 'largest', weight: true),
        $textColor(variant: $variant, contrast: $contrast ?? 'strong'),
        '[&:has(+[data-tallkit-text])]:mb-2 [[data-tallkit-text]+&]:mt-2'
    )"
>
    {{ $slot }}
</tk:element.wrapper>
