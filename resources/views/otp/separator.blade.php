@aware(['size'])
@props(['size'])
<tk:text
    :attributes="$attributes->classes(TALLKit::paddingInline(size: $size, mode: 'smallest'))"
    :$size
    label="&mdash;"
    as="span"
>
    {{ $slot }}
</tk:text>
