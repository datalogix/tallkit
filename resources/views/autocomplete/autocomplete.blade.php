@props([
    'items' => null,
    'size' => null,
    'options' => null,
])
@php

$listboxId = TALLKit::attributesAfter($attributes, 'items:')->get('id', TALLKit::generateId('listbox', $attributes->get('name')));

@endphp
<div
    wire:ignore.self
    x-data="autocomplete(@js($options))"
    {{ TALLKit::attributesAfter($attributes, 'container:')->classes('[:where(&)]:w-full relative') }}
>
    <tk:input
        :attributes="$attributes->whereDoesntStartWith(['container:', 'popover:', 'items:'])"
        :$size
        role="combobox"
        aria-autocomplete="list"
        aria-haspopup="listbox"
        :aria-controls="$listboxId"
        :aria-expanded="opened ? 'true' : 'false'"
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
            :id="$listboxId"
        >
            {{ $slot}}
        </tk:listbox.items>
    </tk:popover>
</div>
