<div
    x-data="menu"
    popover="manual"
    role="menu"
    {{
        $attributes->whereDoesntStartWith(['item:'])
        ->classes('
            [:where(&)]:min-w-48
           [:where(&)]:bg-white dark:[:where(&)]:bg-zinc-700
           [:where(&)]:text-zinc-700 dark:[:where(&)]:text-white
            text-sm
            p-2
            rounded-lg shadow-xs
            border border-zinc-200 dark:border-white/10
            focus:outline-hidden
        ')
        ->merge(['data-keep-open' => $keepOpen])
    }}
>
    {{ $prepend ?? '' }}

    @foreach (collect($items) as $item)
        <tk:menu.item
            :attributes="$attributesAfter('item:')->merge($item)"
            :$size
        />
    @endforeach

    {{ $slot }}

    {{ $append ?? '' }}
</div>
