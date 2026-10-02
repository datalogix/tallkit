@props([
    ...TALLKit::elementProps(),
    'size' => null,
])
<tk:field.group.side
    side="prefix"
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::elementProps())"
    :$size
>
    {{ $slot }}
</tk:field.group.side>
