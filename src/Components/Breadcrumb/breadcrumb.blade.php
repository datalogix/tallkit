@if ($slot->hasActualContent() || collect($items)->isNotEmpty())
    @if (Str::of($slot)->trim()->startsWith('<nav'))
        {{ $slot }}
    @else
        <nav {{ $attributes->whereDoesntStartWith(['item:'])->classes('flex', $fontSize($size, true)) }}>
            @foreach (collect($items) as $item)
                <tk:breadcrumb.item
                    :attributes="$attributesAfter('item:')->merge($item, false)"
                />
            @endforeach

            {{ $slot }}
        </nav>
    @endif
@endif
