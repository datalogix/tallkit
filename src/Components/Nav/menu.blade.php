<tk:dropdown :attributes="$attributesAfter('dropdown:')">
    <tk:nav.item
        :attributes="$attributes->whereDoesntStartWith(['dropdown:', 'menu:'])"
        :$size
        icon-trailing="chevron-down"
    >
        {{ $label ?? '' }}
    </tk:nav.item>

    <tk:menu
        :attributes="$attributesAfter('menu:')"
        :$items
        :$size
    >
        {{ $slot }}
    </tk:menu>
</tk:dropdown>
