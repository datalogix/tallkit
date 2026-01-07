@if ($slot->hasActualContent() || collect($items)->isNotEmpty())
    @if (Str::of($slot)->trim()->startsWith('<nav'))
        {{ $slot }}
    @else
        <nav {{
            $attributes->whereDoesntStartWith(['item:'])
                ->classes(
                    'flex flex-1 overflow-auto',
                    'text-zinc-500 dark:text-white/80',
                    $fontSize($size, true),
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
