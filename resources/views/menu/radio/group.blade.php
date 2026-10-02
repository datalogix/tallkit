@props([
    'items' => null,
    'value' => null,
    'keepOpen' => null,
    'label' => null,
])
<tk:menu.choice-group
    type="radio"
    :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), ['items' => null, 'value' => null, 'keepOpen' => null, 'label' => null])"
>
    {{ $slot }}
</tk:menu.choice-group>
