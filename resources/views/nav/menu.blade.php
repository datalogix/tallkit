@aware(['size'])
@props([
    'size' => null,
    'items' => null,
])
<tk:dropdown :attributes="TALLKit::attributesAfter($attributes, 'dropdown:')">
    <tk:nav.item
        :attributes="$attributes->except(['href'])->whereDoesntStartWith(['dropdown:', 'menu:'])"
        :$size
        iconTrailing="chevron-down"
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
