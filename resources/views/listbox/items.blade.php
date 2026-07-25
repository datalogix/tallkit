@props([
    'items' => null,
    'size' => null,
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
                    focus-visible:outline-blue-700
                    dark:focus-visible:outline-blue-300
                    focus-visible:outline-offset-0

                    focus-visible:ring-2
                    focus-visible:ring-blue-700/20
                    dark:focus-visible:ring-blue-300/20
                ',
                TALLKit::roundedSize(size: $size),
                TALLKit::spaceBlock(size: $size, mode: 'smallest')
            )
    }}
    role="listbox"
    x-bind:tabindex="filteredItems.length > 0 ? 0 : -1"
>
    {{ $slot }}

    @foreach (collect($items) as $item)
        <tk:listbox.item
            :attributes="TALLKit::attributesAfter($attributes, 'item:')->merge(is_array($item) ? $item : ['label' => $item], false)"
            :$size
        />
    @endforeach
</ul>
