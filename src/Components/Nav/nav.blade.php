@if ($slot->isNotEmpty() || collect($items)->isNotEmpty())
    <nav {{
        $attributes->whereDoesntStartWith(['item:'])
            ->classes(
                'flex',
                'text-zinc-500 dark:text-white/80',
                match($size) {
                    'xs' => 'text-[11px] font-normal',
                    'sm' => 'text-xs font-normal',
                    default => 'text-sm font-medium',
                    'lg' => 'text-base font-medium',
                    'xl' => 'text-lg font-semibold',
                    '2xl' => 'text-xl font-semibold',
                    '3xl' => 'text-xl font-bold',
                }
            )
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
@endif
