@props([
    'mode' => null,
    'items' => null,
])
@if (collect($items)->isNotEmpty() || $slot->isNotEmpty())
    <ul
        {{
            $attributes
                ->dataKey('list')
                ->whereDoesntStartWith(['li:', 'item:'])
                ->when($mode === 'none', fn ($attrs) => $attrs->merge(['role' => 'list']))
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
        {{ $slot }}

        @foreach (collect($items) as $index => $item)
            <li
                {{
                    TALLKit::attributesAfter($attributes, 'li:')
                        ->merge(in_livewire() ? ['wire:key' => TALLKit::generateId('list-item', (string) $index)] : [], false)
                }}
            >
                <tk:text
                    :attributes="TALLKit::attributesAfter($attributes, 'item:')"
                    :label="$item"
                />
            </li>
        @endforeach
    </ul>
@endif
