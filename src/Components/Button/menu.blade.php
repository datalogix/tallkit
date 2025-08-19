<tk:dropdown :attributes="$attributesAfter('dropdown:')">
    <tk:button
        :attributes="$attributes->whereDoesntStartWith(['dropdown:', 'menu:', 'item:'])"
        variant="ghost"
        icon="ellipsis-vertical"
    />

    <tk:menu :attributes="$attributesAfter('menu:')">
        @foreach (collect($items) as $item)
            <tk:menu.item :attributes="$attributesAfter('item:')->merge($item)" />
        @endforeach

        {{ $slot }}
    </tk:menu>
</tk:dropdown>
