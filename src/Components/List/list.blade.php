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
            <li>
                <tk:text
                    :attributes="$attributesAfter('item:')"
                    :label="$item"
                    as="span"
                />
            </li>
        @endforeach
    </ul>
@endif
