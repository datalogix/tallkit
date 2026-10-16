@props([
    'items' => null,
    'value' => null,
    'keepOpen' => null,
    'label' => null,
])
<tk:menu.choice-group
    type="radio"
    :attributes="$attributes->withProps(get_defined_vars(), ['items' => null, 'value' => null, 'keepOpen' => null, 'label' => null])->except('type')"
>
    {{ $slot }}
</tk:menu.choice-group>
