@props([
    ...TALLKit::fieldProps(),
    ...TALLKit::fieldControlProps(),
    'size' => null,
    'hideEmpty' => true,
    'clearOnSelect' => false,
    'fuseOptions' => null,
])
@php

$items = collect(TALLKit::parseOptions(attributes: $attributes))
    ->flatMap(fn ($label, $value) => is_array($label) ? array_values($label) : [$label])
    ->all();
$config = ['hideEmpty' => $hideEmpty, 'clearOnSelect' => $clearOnSelect, ...($fuseOptions ?? [])];

$listboxId = $attributes->prefixed('items:')->get('id') ?? TALLKit::stableId('listbox', $attributes->get('name'));

@endphp
<div
    wire:ignore.self
    x-data="autocomplete(@js($config))"
    {{
        $attributes->prefixed('container:')
            ->classes('[:where(&)]:w-full relative')
    }}
>
    <tk:input
        :attributes="TALLKit::fieldWithProps($attributes, get_defined_vars())->whereDoesntStartWith(['container:', 'popover:', 'items:'])"
        :$size
        role="combobox"
        aria-autocomplete="list"
        aria-haspopup="listbox"
        :aria-controls="$listboxId"
        ::aria-expanded="opened ? 'true' : 'false'"
        autocomplete="off"
    />

    <tk:popover
        :attributes="$attributes->prefixed('popover:')"
        :$size
        animation="none"
    >
        <tk:listbox.items
            :attributes="$attributes->prefixed('items:')"
            :$items
            :$size
            :id="$listboxId"
        >
            {{ $slot}}
        </tk:listbox.items>
    </tk:popover>
</div>
