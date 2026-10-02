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
    'label' => null,
])
@php

$hasSearchable = isset($search) || $searchable !== false;
$listId = $attributes->get('items:id') ?? TALLKit::stableId('listbox');

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
            :attributes="$attributes->prefixed('search:')"
            :$size
            :controls="$listId"
        />
    @endisset

    <tk:listbox.items
        :attributes="$attributes->prefixed('items:', with: ['item:'])
            ->when($hasSearchable, fn ($attrs) => $attrs->classes(TALLKit::generateClassBySize(size: $size, name: 'max-h', values: ['48', '56', '64', '72', '80', '88', '96'])))
            ->mergeDefined(['aria-label' => $label ? __($label) : null])
            ->merge(['id' => $listId])
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
                :attributes="$attributes->prefixed('no-records:')"
                :$size
                :label="is_string($noRecords) ? $noRecords : null"
            />
        @endisset
    @endif
</div>
