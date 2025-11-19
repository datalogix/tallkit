<tk:dropdown :attributes="$attributesAfter('dropdown:')">
    <tk:button
        :attributes="$attributes->whereDoesntStartWith(['dropdown:', 'menu:'])"
        :$size
        variant="ghost"
        icon="ellipsis-vertical"
    />

    <tk:menu
        :attributes="$attributesAfter('menu:')"
        :$items
        :$size
    >
        {{ $slot }}
    </tk:menu>
</tk:dropdown>
