@props([
    'items' => null,
    'size' => null,
])
<tk:dropdown :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'dropdown:')">
    <tk:button
        :attributes="$attributes->whereDoesntStartWith(['dropdown:', 'menu:'])"
        :$size
        variant="ghost"
        icon="ellipsis-vertical"
        tooltip="More options"
    />

    <tk:menu
        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'menu:')"
        :$items
        :$size
    >
        {{ $slot }}
    </tk:menu>
</tk:dropdown>
