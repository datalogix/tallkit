@if ($slot->hasActualContent() || collect($items)->isNotEmpty())
    @if (Str::of($slot->toHtml())->trim()->startsWith('<nav'))
        {{ $slot }}
    @else
        <nav {{
            $attributes->whereDoesntStartWith(['item:'])
                ->classes(
                    'flex flex-1 overflow-auto',
                    'text-zinc-500 dark:text-white/80',
                    match($size) {
                        'xs' => 'text-[11px] font-normal',
                        'sm' => 'text-xs font-normal',
                        default => 'text-sm font-medium',
                        'lg' => 'text-base font-medium',
                        'xl' => 'text-lg font-semibold',
                        '2xl' => 'text-xl font-semibold',
                        '3xl' => 'text-2xl font-bold',
                    }
                )
                ->when(
                    $list,
                    fn($attrs) => $attrs->classes('gap-1 flex-col overflow-visible min-h-auto'),
                    fn($attrs) => $attrs->classes(['gap-2.5 items-center py-3', 'overflow-x-auto overflow-y-hidden' => $scrollable]),
                )
        }}>
            @foreach (collect($items) as $item)
                <tk:nav.item
                    :attributes="$attributesAfter('item:')->merge($item, false)"
                />
            @endforeach

            {{ $slot }}
        </nav>
    @endif
@endif
