@props([
    'items' => null,
    'size' => null,
    'keepOpen' => null,
    'animation' => null,
])
<tk:popover
    x-data="menu"
    role="menu"
    :$size
    :$keepOpen
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
                '[:where(&)]:max-w-[min(24rem,calc(100vw-1rem))]',
            )
    }}
>
    {{ $prepend ?? '' }}

    @foreach (collect($items) as $item)
        @if ($item)
            <tk:menu.item
                :attributes="$attributes->prefixed('item:')
                    ->merge(TALLKit::attributesFromItem($item), false)
                "
                :$size
            />
        @endif

        @if (empty($item) || data_get($item, 'separator') === true)
            <tk:menu.separator :attributes="$attributes->prefixed('separator:')" />
        @endif
    @endforeach

    {{ $slot }}

    {{ $append ?? '' }}
</tk:popover>
