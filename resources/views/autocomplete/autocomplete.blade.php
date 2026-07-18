@props([
    'items' => null,
    'size' => null,
    'options' => null,
])
<div
    wire:ignore.self
    x-data="autocomplete(@js($options))"
    {{ TALLKit::attributesAfter($attributes, 'container:')->classes('[:where(&)]:w-full relative') }}
>
    <tk:input
        :attributes="$attributes->whereDoesntStartWith(['container:', 'popover:', 'items:'])"
        :$size
        autocomplete="off"
    />

    <tk:popover
        :attributes="TALLKit::attributesAfter($attributes, 'popover:')"
        :$size
    >
        <tk:listbox.items
            :attributes="TALLKit::attributesAfter($attributes, 'items:')"
            :$items
            :$size
        >
            {{ $slot}}
        </tk:listbox.items>
    </tk:popover>
</div>
