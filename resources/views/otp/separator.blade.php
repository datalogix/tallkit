@aware(['size'])
@props([
    ...TALLKit::elementProps(),
    'size' => null,
    'label' => '—',
])
<tk:text
    aria-hidden="true"
    :attributes="$attributes->withElementProps(get_defined_vars())->classes(TALLKit::paddingInline(size: $size, mode: 'smallest'))"
    :$size
>
    {{ $slot }}
</tk:text>
