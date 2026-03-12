@if (collect($items)->isNotEmpty())
    <ul {{ $attributes->whereDoesntStartWith(['item:'])->classes(
        match ($mode) {
            'none' => 'list-none',
            default => 'list-disc',
            'decimal' => 'list-decimal',
        },
        'list-inside',
    ) }}>
        @foreach (collect($items) as $item)
            <tk:text
                :attributes="$attributesAfter('item:')"
                :label="$item"
                as="li"
            />
        @endforeach
    </ul>
@endif
