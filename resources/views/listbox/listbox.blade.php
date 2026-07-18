@props([
    'items' => null,
    'size' => null,
    'searchable' => null,
    'noRecords' => null,
    'hideEmpty' => null,
    'clearOnSelect' => null,
    'fuseOptions' => null,
])
<div
    wire:ignore.self
    x-data="listbox({ hideEmpty: @js($hideEmpty), clearOnSelect: @js($clearOnSelect), ...@js($fuseOptions) })"
    {{
        $attributes
            ->whereDoesntStartWith(['input:', 'items:', 'item:', 'no-records:'])
            ->classes(TALLKit::spaceBlock(size: $size, mode: 'small'))
    }}
>
    @isset ($input)
        {{ $input }}
    @elseif ($searchable !== false)
        <tk:listbox.input
            :attributes="TALLKit::attributesAfter($attributes, 'input:')"
            :$size
        />
    @endisset

    <tk:listbox.items
        :attributes="TALLKit::attributesAfter($attributes, 'items:', prepend: ['item:'])
            ->when(
                isset($input) || $searchable !== false,
                fn ($attributes) => $attributes->classes(TALLKit::generateClassBySize(size: $size, name: 'max-h', values: ['48', '56', '64', '72', '80', '88', '96']))
            )
        "
        :$items
        :$size
    >
        {{ $slot}}
    </tk:listbox.items>

    @if (isset($input) || $searchable !== false)
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
