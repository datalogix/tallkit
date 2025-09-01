<tk:dropdown :attributes="$attributesAfter('dropdown:')">
    <tk:button
        :attributes="$attributes->whereDoesntStartWith(['dropdown:', 'menu:'])"
        variant="ghost"
        icon="ellipsis-vertical"
    />

    <tk:menu
        :attributes="$attributesAfter('menu:')"
        :$items
    >
        {{ $slot }}
    </tk:menu>
</tk:dropdown>
