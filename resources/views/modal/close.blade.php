@props([
    ...TALLKit::elementProps(),
])
<tk:button :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::elementProps())->dataKey('modal-close')">
    {{ $slot }}
</tk:button>
