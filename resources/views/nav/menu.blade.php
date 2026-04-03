@props([
    'items' => null,
    'size' => null,
])
<tk:dropdown :attributes="TALLKit::attributesAfter($attributes, 'dropdown:')">
    <tk:nav.item
        :attributes="$attributes->whereDoesntStartWith(['dropdown:', 'menu:'])"
        :$size
        icon-trailing="chevron-down"
    >
        {{ $label ?? '' }}
    </tk:nav.item>

    <tk:menu
        :attributes="TALLKit::attributesAfter($attributes, 'menu:')"
        :$items
        :$size
    >
        {{ $slot }}
    </tk:menu>
</tk:dropdown>
