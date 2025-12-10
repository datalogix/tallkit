@if ($slot->hasActualContent() || collect($items)->isNotEmpty())
    @if (Str::of($slot->toHtml())->trim()->startsWith('<nav'))
        {{ $slot }}
    @else
        <nav {{
            $attributes->whereDoesntStartWith(['item:'])
                ->classes(
                    'flex',
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
        }}>
            @foreach (collect($items) as $item)
                <tk:breadcrumb.item
                    :attributes="$attributesAfter('item:')->merge($item, false)"
                />
            @endforeach

            {{ $slot }}
        </nav>
    @endif
@endif
