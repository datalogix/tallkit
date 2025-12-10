<div
    x-data="menu"
    popover="manual"
    role="menu"
    {{
        $attributes
            ->whereDoesntStartWith(['item:', 'separator:'])
            ->classes('
                [:where(&)]:min-w-48
                [:where(&)]:bg-white dark:[:where(&)]:bg-zinc-700
                [:where(&)]:text-zinc-700 dark:[:where(&)]:text-white
                text-sm
                p-2
                rounded-lg shadow-xs
                border border-zinc-200 dark:border-white/10
                focus:outline-hidden
                [&>[data-tallkit-menu-separator-container]:first-child]:hidden
                [&>[data-tallkit-menu-separator-container]:last-child]:hidden
                [&_[data-tallkit-menu-separator-container]:has(+[data-tallkit-menu-separator-container])]:hidden
            ')
            ->merge(['data-keep-open' => $keepOpen])
    }}
>
    {{ $prepend ?? '' }}

    @foreach (collect($items) as $item)
        @if ($item)
            <tk:menu.item
                :attributes="$attributesAfter('item:')->merge($item, false)"
                :$size
            />
        @endif

         @if (empty($item) || data_get($item, 'separator') === true)
            <tk:menu.separator :attributes="$attributesAfter('separator:')" />
        @endif
    @endforeach

    {{ $slot }}

    {{ $append ?? '' }}
</div>
