@props([
    'items' => null,
    'size' => null,
    'animation' => null,
])
<tk:popover
    x-data="menu"
    role="menu"
    :$size
    :$animation
    {{
        $attributes
            ->dataKey('menu')
            ->whereDoesntStartWith(['item:', 'separator:'])
            ->classes(
                '
                    [&>[data-tallkit-menu-separator-container]:first-child]:hidden
                    [&>[data-tallkit-menu-separator-container]:last-child]:hidden
                    [&_[data-tallkit-menu-separator-container]:has(+[data-tallkit-menu-separator-container])]:hidden
                ',
            )
    }}
>
    {{ $prepend ?? '' }}

    @foreach (collect($items) as $item)
        @if ($item)
            <tk:menu.item
                :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'item:')->merge(is_array($item) ? $item : ['label' => $item], false)"
                :$size
            />
        @endif

        @if (empty($item) || data_get($item, 'separator') === true)
            <tk:menu.separator :attributes="TALLKit::attributesAfter(attributes: $attributes, prefix: 'separator:')" />
        @endif
    @endforeach

    {{ $slot }}

    {{ $append ?? '' }}
</tk:popover>
