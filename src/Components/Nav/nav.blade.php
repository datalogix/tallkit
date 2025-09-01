<nav {{
    $attributes->whereDoesntStartWith(['item:'])
        ->classes('flex')
        ->when(
            $list,
            fn($attrs) => $attrs->classes('gap-1 flex-col overflow-visible min-h-auto'),
            fn($attrs) => $attrs->classes(['gap-2.5', 'items-center py-3', 'overflow-x-auto overflow-y-hidden' => $scrollable]),
        )
}}>
    @foreach (collect($items) as $item)
        <tk:nav.item
            :attributes="$attributesAfter('item:')->merge($item)"
        />
    @endforeach

    {{ $slot }}
</nav>
