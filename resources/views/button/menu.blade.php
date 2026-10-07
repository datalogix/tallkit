@props([
    ...TALLKit::elementProps(),
    'items' => null,
    'size' => null,
    'keepOpen' => null,
    'animation' => null,
])
<tk:dropdown :attributes="$attributes->prefixed('dropdown:')">
    <tk:button
        :attributes="TALLKit::attributesWithProps($attributes, get_defined_vars(), TALLKit::elementProps())->whereDoesntStartWith(['dropdown:', 'menu:'])
            ->merge(['variant' => 'ghost', 'icon' => 'ellipsis-vertical', 'tooltip' => 'More options'])"
        :$size
    />

    <tk:menu
        :attributes="$attributes->prefixed('menu:')"
        :$items
        :$size
        :$keepOpen
        :$animation
    >
        {{ $slot }}
    </tk:menu>
</tk:dropdown>
