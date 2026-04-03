@props([
    'items' => null,
    'size' => null,
])
<tk:dropdown :attributes="TALLKit::attributesAfter($attributes, 'dropdown:')">
    <tk:button
        :attributes="$attributes->whereDoesntStartWith(['dropdown:', 'menu:'])"
        :$size
        variant="ghost"
        icon="ellipsis-vertical"
    />

    <tk:menu
        :attributes="TALLKit::attributesAfter($attributes, 'menu:')"
        :$items
        :$size
    >
        {{ $slot }}
    </tk:menu>
</tk:dropdown>
