@if ($slot->hasActualContent() || collect($items)->isNotEmpty())
    @if (Str::of($slot)->trim()->startsWith('<nav'))
        {{ $slot }}
    @else
        <nav {{ $attributes->whereDoesntStartWith(['item:'])->classes(
            'flex',
            $fontSize(size: $size, mode: $mode, weight: true),
            $textColor(variant: $variant, contrast: $contrast),
        ) }}>
            @foreach (collect($items) as $item)
                <tk:breadcrumb.item
                    :attributes="$attributesAfter('item:')->merge(is_array($item) ? $item : ['label' => $item], false)"
                    :$size
                />
            @endforeach

            {{ $slot }}
        </nav>
    @endif
@endif
