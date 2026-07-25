@props([
    'items' => null,
    'size' => null,
    'searchable' => null,
    'noRecords' => null,
    'hideEmpty' => null,
    'clearOnSelect' => null,
    'fuseOptions' => null,
    'standalone' => null,
])
<div
    wire:ignore.self
    @if ($standalone !== false)
        x-data="listbox({ hideEmpty: @js($hideEmpty), clearOnSelect: @js($clearOnSelect), ...@js($fuseOptions) })"
    @endif
    {{
        $attributes
            ->whereDoesntStartWith(['search:', 'items:', 'item:', 'no-records:'])
            ->classes(TALLKit::spaceBlock(size: $size, mode: 'small'))
    }}
>
    @isset ($search)
        {{ $search }}
    @elseif ($searchable !== false)
        <tk:listbox.search
            :attributes="TALLKit::attributesAfter($attributes, 'search:')"
            :$size
        />
    @endisset

    <tk:listbox.items
        :attributes="TALLKit::attributesAfter($attributes, 'items:', prepend: ['item:'])
            ->when(
                isset($search) || $searchable !== false,
                fn ($attributes) => $attributes->classes(TALLKit::generateClassBySize(size: $size, name: 'max-h', values: ['48', '56', '64', '72', '80', '88', '96']))
            )
        "
        :$items
        :$size
    >
        {{ $slot}}
    </tk:listbox.items>

    @if (isset($search) || $searchable !== false)
        @isset ($empty)
            {{ $empty }}
        @elseif ($noRecords !== false)
            <tk:listbox.no-records
                :attributes="TALLKit::attributesAfter($attributes, 'no-records:')"
                :$size
            />
        @endisset
    @endif
</div>
