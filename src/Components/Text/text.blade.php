<tk:element.wrapper
    as="p"
    :name="$baseComponentKey()"
    :attributes="$attributes->classes(
        $fontSize(size: $size, mode: $mode, weight: $weight),
        $textColor(variant: $variant, contrast: $contrast),
        $iconSize(size: $size),
    )"
>
    {{ $slot }}
</tk:element.wrapper>
