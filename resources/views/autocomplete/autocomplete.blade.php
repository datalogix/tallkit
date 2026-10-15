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

$listboxId = $attributes->prefixed('items:')->get('id') ?? TALLKit::stableId('listbox', $attributes->get('name'));

@endphp
<div
    wire:ignore.self
    x-data="autocomplete({{ Js::from([
        'hideEmpty' => $hideEmpty,
        'clearOnSelect' => $clearOnSelect,
        'texts' => [
            'none' => __('No records found'),
            'one' => __(':count result available'),
            'other' => __(':count results available'),
        ],
        ...($fuseOptions ?? []),
    ]) }})"
    {{
        $attributes->prefixed('container:')
            ->classes('[:where(&)]:w-full relative')
    }}
>
    <tk:input
        :attributes="$attributes->withFieldProps(get_defined_vars(), TALLKit::fieldControlProps())->whereDoesntStartWith(['container:', 'popover:', 'items:'])"
        :$size
        role="combobox"
        aria-autocomplete="list"
        aria-haspopup="listbox"
        :aria-controls="$listboxId"
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
            {{ $slot }}
        </tk:listbox.items>
    </tk:popover>
</div>
