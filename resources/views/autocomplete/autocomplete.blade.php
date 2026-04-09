@props([
    'items' => null,
    'size' => null,
    'options' => null,
])
<div
    wire:ignore.self
    x-data="autocomplete(@js($options ?? []))"
    {{ TALLKit::attributesAfter($attributes, 'container:')->classes('[:where(&)]:w-full') }}
>
    <tk:input
        :attributes="$attributes->whereDoesntStartWith(['container:', 'popover:', 'items:'])"
        :$size
        autocomplete="off"
    />

    <tk:popover
        :attributes="TALLKit::attributesAfter($attributes, 'popover:')->classes('p-0')"
        :$size
    >
        <tk:autocomplete.items
            :attributes="TALLKit::attributesAfter($attributes, 'items:')"
            :$items
            :$size
        >
            {{ $slot }}
        </tk:autocomplete.items>
    </tk:popover>
</div>
