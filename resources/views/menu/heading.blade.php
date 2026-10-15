@aware(['size'])
@props([
    ...TALLKit::elementProps(),
    'label' => null,
    'size' => null,
])
<tk:heading
    :attributes="$attributes->withElementProps(get_defined_vars())->classes(
        'w-full justify-start',
        TALLKit::textNeutral(variant: 'subtle'),
        TALLKit::padding(size: $size, mode: 'smallest'
    ))"
    :size="TALLKit::adjustSize(size: $size)"
    :$label
>
    {{ $slot }}
</tk:heading>
