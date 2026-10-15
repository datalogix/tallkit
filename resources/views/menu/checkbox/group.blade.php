@props([
    'items' => null,
    'value' => null,
    'keepOpen' => null,
    'label' => null,
])
<tk:menu.choice-group
    type="checkbox"
    :attributes="$attributes->withProps(get_defined_vars(), ['items' => null, 'value' => null, 'keepOpen' => null, 'label' => null])"
>
    {{ $slot }}
</tk:menu.choice-group>
