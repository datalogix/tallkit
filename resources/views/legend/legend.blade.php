@props([
    ...TALLKit::elementProps(),
    'label' => null,
])
<tk:heading
    kind="legend"
    as="legend"
    :$label
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::elementProps())"
>
    {{ $slot }}
</tk:heading>
