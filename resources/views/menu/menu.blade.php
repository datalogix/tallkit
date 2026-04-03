@props([
    'keepOpen' => null,
    'items' => null,
    'size' => null,
])
<div
    x-data="menu"
    popover="manual"
    role="menu"
    {{
        $attributes
            ->dataKey('menu')
            ->whereDoesntStartWith(['item:', 'separator:'])
            ->classes(
                '
                    [:where(&)]:bg-white dark:[:where(&)]:bg-zinc-700
                    [:where(&)]:text-zinc-700 dark:[:where(&)]:text-white
                    border border-zinc-200 dark:border-white/10
                    shadow-xs focus:outline-hidden
                    [&>[data-tallkit-menu-separator-container]:first-child]:hidden
                    [&>[data-tallkit-menu-separator-container]:last-child]:hidden
                    [&_[data-tallkit-menu-separator-container]:has(+[data-tallkit-menu-separator-container])]:hidden
                ',
                TALLKit::fontSize(size: $size),
                TALLKit::roundedSize(size: $size, mode: 'large'),
                match ($size) {
                    'xs' => '[:where(&)]:min-w-32 p-1',
                    'sm' => '[:where(&)]:min-w-40 p-1.5',
                    default => '[:where(&)]:min-w-48 p-2',
                    'lg' => '[:where(&)]:min-w-56 p-2.5',
                    'xl' => '[:where(&)]:min-w-64 p-3',
                    '2xl' => '[:where(&)]:min-w-72 p-3.5',
                    '3xl' => '[:where(&)]:min-w-80 p-4',
                },
            )
            ->merge(['data-keep-open' => $keepOpen])
    }}
>
    {{ $prepend ?? '' }}

    @foreach (collect($items) as $item)
        @if ($item)
            <tk:menu.item
                :attributes="TALLKit::attributesAfter($attributes, 'item:')->merge(is_array($item) ? $item : ['label' => $item], false)"
                :$size
            />
        @endif

         @if (empty($item) || data_get($item, 'separator') === true)
            <tk:menu.separator :attributes="TALLKit::attributesAfter($attributes, 'separator:')" />
        @endif
    @endforeach

    {{ $slot }}

    {{ $append ?? '' }}
</div>
