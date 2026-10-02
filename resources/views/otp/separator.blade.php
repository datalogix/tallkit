@aware(['size'])
@props([
    ...TALLKit::elementProps(),
    'size' => null,
    'label' => '—',
])
<tk:text
    aria-hidden="true"
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::elementProps())->classes(TALLKit::paddingInline(size: $size, mode: 'smallest'))"
    :$size
>
    {{ $slot }}
</tk:text>
