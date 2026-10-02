@props([
    'marker' => null,
    'items' => null,
])
@php
$tag = $marker === 'decimal' ? 'ol' : 'ul';
@endphp
@if (collect($items)->isNotEmpty() || $slot->isNotEmpty())
    <{{ $tag }}
        {{
            $attributes
                ->dataKey('list')
                ->whereDoesntStartWith(['li:', 'item:'])
                ->when($marker === 'none', fn ($attrs) => $attrs->merge(['role' => 'list']))
                ->classes(
                    'list-inside',
                    match ($marker) {
                        'none' => 'list-none',
                        default => 'list-disc',
                        'decimal' => 'list-decimal',
                    },
                )
        }}
    >
        {{ $slot }}

        @foreach (collect($items) as $index => $item)
            <li
                {{
                    $attributes->prefixed('li:')
                        ->wireKey(TALLKit::generateId(prefix: 'list-item', name: (string) $index))
                }}
            >
                <tk:text
                    :attributes="$attributes->prefixed('item:')"
                    :label="$item"
                    as="span"
                />
            </li>
        @endforeach
    </{{ $tag }}>
@endif
