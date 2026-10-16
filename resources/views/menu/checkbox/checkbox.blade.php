@props([
    ...TALLKit::elementProps(),
    'checked' => null,
    'iconOn' => null,
    'iconOff' => null,
])
<tk:menu.choice
    type="checkbox"
    :attributes="$attributes->withElementProps(get_defined_vars(), ['checked' => null, 'iconOn' => null, 'iconOff' => null, 'keepOpen' => null])->except('type')"
>
    {{ $slot }}
</tk:menu.choice>
