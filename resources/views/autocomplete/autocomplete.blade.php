@props([
    'items' => null,
    'size' => null,
    'options' => null,
    'animation' => null,
])
@php

$listboxId = TALLKit::attributesAfter(attributes: $attributes, prefix: 'items:')->get('id', TALLKit::generateId(prefix: 'listbox', name: $attributes->get('name')));

@endphp
<div
    wire:ignore.self
    x-data="autocomplete(@js($options))"
    {{ TALLKit::attributesAfter(attributes: $attributes, prefix: 'container:')->classes('[:where(&)]:w-full relative') }}
>
    <tk:input
        :attributes="$attributes->whereDoesntStartWith(['container:', 'popover:', 'items:'])"
        :$size
        role="combobox"
        aria-autocomplete="list"
        aria-haspopup="listbox"
        :aria-controls="$listboxId"
        ::aria-expanded="opened ? 'true' : 'false'"
        autocomplete="off"
    />

    <tk:popover
        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'popover:')"
        :$size
        :animation="$animation ?? 'none'"
    >
        <tk:listbox.items
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'items:')"
            :$items
            :$size
            :id="$listboxId"
        >
            {{ $slot}}
        </tk:listbox.items>
    </tk:popover>
</div>
