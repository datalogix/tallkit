@props([
    ...TALLKit::elementProps(),
    'label' => 'Send',
])
<tk:button
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::elementProps())"
    type="submit"
>
    {{ $slot }}
</tk:button>
