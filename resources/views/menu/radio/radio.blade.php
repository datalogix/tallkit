@props([
    ...TALLKit::elementProps(),
    'checked' => null,
    'iconOn' => null,
    'iconOff' => null,
])
<tk:menu.choice
    type="radio"
    :attributes="$attributes->withElementProps(get_defined_vars(), ['checked' => null, 'iconOn' => null, 'iconOff' => null, 'keepOpen' => null])"
>
    {{ $slot }}
</tk:menu.choice>
