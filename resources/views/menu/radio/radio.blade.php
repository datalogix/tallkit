@props([
    ...TALLKit::elementProps(),
    'checked' => null,
    'iconOn' => null,
    'iconOff' => null,
])
<tk:menu.choice
    type="radio"
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::elementProps(), ['checked' => null, 'iconOn' => null, 'iconOff' => null, 'keepOpen' => null])"
>
    {{ $slot }}
</tk:menu.choice>
