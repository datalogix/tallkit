@props([
    'items' => null,
    'size' => null,
])
<ul
    {{
        $attributes
            ->whereDoesntStartWith(['item:'])
            ->classes(
                'overflow-auto',
                TALLKit::spaceBlock(size: $size, mode: 'smallest')
            )
    }}
    role="listbox"
    tabindex="-1"
>
    {{ $slot }}

    @foreach (collect($items) as $item)
        <tk:listbox.item
            :attributes="TALLKit::attributesAfter($attributes, 'item:')->merge(is_array($item) ? $item : ['label' => $item], false)"
            :$size
        />
    @endforeach
</ul>
