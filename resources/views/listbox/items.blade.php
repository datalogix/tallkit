@props([
    'items' => null,
    'size' => null,
    'multiple' => null,
    'color' => null,
])
<ul
    {{
        $attributes
            ->whereDoesntStartWith(['item:'])
            ->classes(
                '
                    overflow-auto
                    overscroll-contain
                    scroll-py-1

                    outline-none
                    focus-visible:outline-2
                    focus-visible:outline-offset-0
                    focus-visible:ring-2
                ',
                TALLKit::controlFocusRing(color: $color) ?? 'tk-control-focus-ring',
                TALLKit::roundedSize(size: $size),
                TALLKit::spaceBlock(size: $size, mode: 'smallest')
            )
            ->when($multiple, fn ($attrs) => $attrs->merge(['aria-multiselectable' => 'true']))
    }}
    role="listbox"
    x-bind:tabindex="filteredItems.length > 0 ? 0 : -1"
>
    {{ $slot }}

    @foreach (collect($items) as $index => $item)
        <tk:listbox.item
            :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'item:')
                ->merge(is_array($item) ? $item : ['label' => $item], false)
                ->merge(in_livewire() ? ['wire:key' => TALLKit::generateId(prefix: 'listbox-item', name: (string) data_get($item, 'value', $index))] : [], false)
            "
            :$size
        />
    @endforeach
</ul>
