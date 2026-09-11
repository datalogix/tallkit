@props([
    'items' => null,
    'size' => null,
    'searchable' => null,
    'noRecords' => null,
    'hideEmpty' => null,
    'clearOnSelect' => null,
    'fuseOptions' => null,
    'standalone' => null,
    'multiple' => null,
    'color' => null,
])
@php

$hasSearchable = isset($search) || $searchable !== false;

@endphp
<div
    wire:ignore.self
    @if ($standalone !== false)
        x-data="listbox({ hideEmpty: @js($hideEmpty), clearOnSelect: @js($clearOnSelect), ...@js($fuseOptions) })"
    @endif
    {{
        $attributes
            ->whereDoesntStartWith(['search:', 'items:', 'item:', 'no-records:'])
                ->when($hasSearchable, fn ($attrs) => $attrs->classes('[:where(&)]:flex [:where(&)]:flex-col', TALLKit::gapBlock(size: $size, mode: 'small')))
    }}
>
    @isset ($search)
        {{ $search }}
    @elseif ($searchable !== false)
        <tk:listbox.search
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'search:')"
            :$size
        />
    @endisset

    <tk:listbox.items
        :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'items:', prepend: ['item:'])
            ->when($hasSearchable, fn ($attrs) => $attrs->classes(TALLKit::generateClassBySize(size: $size, name: 'max-h', values: ['48', '56', '64', '72', '80', '88', '96'])))
        "
        :$items
        :$size
        :$multiple
        :$color
    >
        {{ $slot}}
    </tk:listbox.items>

    @if ($hasSearchable)
        @isset ($empty)
            {{ $empty }}
        @elseif ($noRecords !== false)
            <tk:listbox.no-records
                :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'no-records:')"
                :$size
            />
        @endisset
    @endif
</div>
