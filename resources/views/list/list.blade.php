@props([
    'mode' => null,
    'items' => null,
])
@if (collect($items)->isNotEmpty())
    <ul
        {{
            $attributes
                ->dataKey('list')
                ->whereDoesntStartWith(['item:'])
                ->classes(
                    'list-inside',
                    match ($mode) {
                        'none' => 'list-none',
                        default => 'list-disc',
                        'decimal' => 'list-decimal',
                    },
                )
        }}
    >
        @foreach (collect($items) as $item)
            <li>
                <tk:text
                    :attributes="TALLKit::attributesAfter($attributes, 'item:')"
                    :label="$item"
                    as="span"
                />
            </li>
        @endforeach
    </ul>
@endif
