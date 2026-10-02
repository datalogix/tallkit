@aware(['size'])
@props([
    ...TALLKit::elementProps(),
    'label' => null,
    'size' => null,
])
<tk:heading
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::elementProps())->classes(
        'w-full justify-start',
        TALLKit::textNeutral(variant: 'subtle'),
        TALLKit::padding(size: $size, mode: 'smallest'
    ))"
    :size="TALLKit::adjustSize(size: $size)"
    :$label
>
    {{ $slot }}
</tk:heading>
